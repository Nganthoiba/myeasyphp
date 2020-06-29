<?php
declare(strict_types=1);
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Response;
use MyEasyPHP\Libs\ext\csrf;
use MyEasyPHP\Libs\View;
use MyEasyPHP\Libs\HttpStatus;

function generateRandomString($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/*php function to generate random unique id*/
function randId($length=32){
    $id = (uniqid(). rand().time(). generateRandomString($length));
    $char = str_shuffle($id);
    for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i++) {
        $rand .= $char[mt_rand(0, $l)];
    }
    return $rand;
}

function filter($key,$method){
    if(trim($method)=="POST"){
        $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_ENCODED);
    }else{
        $value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        //$value = filter_input(INPUT_GET, $key, FILTER_SANITIZE_ENCODED);
    }
    if(!is_null($value)){
        $value = trim($value);
    }
    return ($value);
}


function getAuthorizedToken(){
    //function to get authorized token from http headers
    $headers = apache_request_headers();
    $token = get_data_from_array("Authorization",$headers);
    if($token!=null){
        $token = trim(str_replace("Bearer", "", $token));
    }
    return $token;
}

function get_data_from_array($key_data,$array){
    foreach ($array as $key=>$value){
        if($key==$key_data){
            return $value;
        }
    }
    return null;
}

/*function to start session*/
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE){
        ini_set('session.use_only_cookies', 'true'); // Forces sessions to only use cookies. 
        ini_set("session.cookie_httponly", 'true');//(No xxs)This will prevent from javascript to display session cookies over browser
        ini_set('session.httponly','true');/* securing cookies and session*/
        session_start(); // Start the php session
    }
    session_regenerate_id(true); // regenerated the session, delete the old one.
}

/*function to get client ip*/
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')){
        $ipaddress = getenv('HTTP_CLIENT_IP');
    }
    else if(getenv('HTTP_X_FORWARDED_FOR')){
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    }
    else if(getenv('HTTP_X_FORWARDED')){
    $ipaddress = getenv('HTTP_X_FORWARDED');}
    else if(getenv('HTTP_FORWARDED_FOR'))
    {$ipaddress = getenv('HTTP_FORWARDED_FOR');}
    else if(getenv('HTTP_FORWARDED'))
    {$ipaddress = getenv('HTTP_FORWARDED');}
    else if(getenv('REMOTE_ADDR'))
    {$ipaddress = getenv('REMOTE_ADDR');}
    else
    {$ipaddress = 'UNKNOWN';}
    return $ipaddress;
}

function redirect($controller, $action=""){
    header("Location: ".Config::get('host')."/".$controller."/".$action);
}

function isLinkActive($link){
    $link = (!is_null($link))?strtolower($link):"";
    $link = str_replace(Config::get('host'), "", $link);
    
    $current_link = (filter("uri", "GET"));
    $current_link = (!is_null($current_link))?strtolower($current_link):"";
    $current_link = str_replace(Config::get('host'), "", $current_link);
    
    if(trim($link, '/') == trim($current_link,'/')){
        return "active";
    }
    else{
        return "";
    }
}

//CSRF based functions
function writeCSRFToken($csrf_token=""){
    //if token is not passed
    if($csrf_token === ""){
        $csrf_token = csrf::generate('token');
    }
    return "<input type='hidden' name='csrf_token' id='csrf_token' value='".$csrf_token."' />";
}
//function to get existing(already generated) csrf token
function getCSRFToken(){
    return csrf::getToken('token');
}
//function to verify csrf token
function verifyCSRFToken($origin = null): Response{
    $response = new Response();
    try{
        $origin = is_null($origin)?$_REQUEST:$origin;
        // Run CSRF check, on REQUEST(whether POST or GET) data, in exception mode, for 10 minutes, in one-time mode.
        if(csrf::check('token', $origin, true,60*10, true)){
            $response->set(array(
                "msg"=>"token matched",
                "status"=>true,
                "status_code"=>200
            ));
        }
    } catch (Exception $e){
        $response->set(array(
            "msg"=>"An error occurs, CSRF token is expired or invalid. Try again after reloading the page.",
            "status"=>false,
            "status_code"=>403,
            "error"=>$e->getMessage()
        ));
    }
    return $response;
}

//function to get link
function getHtmlLink($controller,$action="",$params = ""){
    $link = Config::get('host')."/".$controller."/".$action;
    if((is_string($params) && trim($params) !== "") || is_numeric($params)){
        $link .= "/".$params;
    }
    else if(is_array($params)){
        foreach ($params as $param){
            $link .= "/".$param;
        }
    }
    return $link;
}

//file reading and writing functions
function downloadFile($file_path,$flag=false){
    if(file_exists($file_path)) {
        header('Content-Description: File Transfer');
        //header('Content-Type: application/octet-stream');
        if($flag == true)
        {
            //download popup will display
            header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
        }
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        flush(); // Flush system output buffer
        readfile($file_path);
        die();
    } else {
        http_response_code(404);
        die();
    }
}

function errorView(int $httpCode,$errorDetails,bool $isPartial = false) : View{
    
    $viewData = new MyEasyPHP\Libs\ViewData();
    $viewData->httpCode = $httpCode;
    $viewData->httpStatus = HttpStatus::getStatus($httpCode);
    $viewData->details = $errorDetails;
    
    //path to error page
    $path = VIEWS_PATH."Shared".DS."error.view.php";
    $view = new View($path,$viewData);
    if($isPartial  == true){
        return $view;
    }
    
    $viewData->content = $view->render();
    
    $layout = Config::get('default_view_container');//$this->router->getRoute();
    $layout_path = VIEWS_PATH.$layout.'.view.php';
    $layout_view_obj = new View($layout_path,$viewData);
    return $layout_view_obj;
}

