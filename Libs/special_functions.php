<?php
/* 
 * GLOBAL FUNCTIONS
 * All the functions defined in this file are globally accessible in the entire 
 * application.
 * 
 * WARNING: Don't change anything in the file even by mistake, otherwise the system 
 * may undergo unwanted behaviours.
 */
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Response;
use MyEasyPHP\Libs\ext\csrf;
use MyEasyPHP\Libs\View;
use MyEasyPHP\Libs\HttpStatus;
use MyEasyPHP\Libs\ViewData;
use MyEasyPHP\Libs\Controller;
/*---***********----------- UTILITY FUNCTIONS----------------------*/
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

/*------------------- HTML RELATED FUNCTIONS ------------------------*/
//redirection to other page
function redirect($controller, $action=""){
    header("Location: ".Config::get('host')."/".$controller."/".$action);
}
function isLinkActive($link){
    $link = (!is_null($link))?strtolower($link):"";
    $link = str_replace(Config::get('host'), "", $link);
    
    $current_link = getCurrentLink();
    
    if(trim($link, '/') == trim($current_link,'/')){
        return "active";
    }
    else{
        return "";
    }
}

function getCurrentLink(){
    $current_link = (filter("uri", "GET"));
    $current_link = (!is_null($current_link))?strtolower($current_link):"";
    $current_link = str_replace(Config::get('host'), "", $current_link);
    return $current_link;
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

/*-------------------- SECURITY FUNCTIONS--------------------*/
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
function verifyCSRFToken($origin = null,$timespan = null,$useTokenMultipleTimes = true): Response{
    $response = new Response();
    try{
        $origin = is_null($origin)?$_REQUEST:$origin;
        // Run CSRF check, on REQUEST(whether POST or GET) data, in exception mode, for 10 minutes, in one-time mode.
        if(csrf::check('token', $origin, true,$timespan/*60*60*20*/, $useTokenMultipleTimes)){
            $response->set(array(
                "msg"=>"token matched",
                "status"=>true,
                "status_code"=>200
            ));
        }
    } catch (Exception $e){
        $response->set(array(
            "msg"=>"An error occurs. ".$e->getMessage()." Try again after reloading the page.",
            "status"=>false,
            "status_code"=>403,
            "error"=>$e->getMessage()
        ));
    }
    return $response;
}

/*--------------------- FILE RELATED FUNCTIONS --------------------------*/
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
        die("File not found.");
    }
}
/*-------------------------------- VIEW FUNCTIONS ----------------------------------*/
//function to return view
//HOW TO USE:-
//The function can consume up to 3 parameters, so there are 4 ways how this function 
//can be accessed:
//1.    pass no argument i.e. simply view(). This will return a default view object
//2.    pass a single argument, this argument can be a path of a view page (string), 
//      or a model object, or an entity object or simply an array or a view data.
//3.    pass 2 argments, then the first argument must be a string which is view path,
//      and the second argument is either a model object, or an entity object, or array
//      or an object of ViewData.
//4.    pass 3 arguments, then the first argument must be a string which is view path,
//      and the second argument is either a model object, or an entity object or an array.
//      And the third argument(last) must be an object of ViewData.
//      
//#Note: If the function is used in a method of a controller, then pass the view data of 
//that controller.
//

function view():View{
    global $router,$controllerObj;
    $viewPath = "";
    $dataModel = null;
    $viewData = new ViewData();
    if($controllerObj instanceof Controller){
        //view data will be set only when the is an instance of controller object
        $viewData = $controllerObj->getViewData();
    }
    $numargs = func_num_args();
    switch($numargs){
        case 0:
            $viewPath = "";
            break;
        case 1:
            $arg = func_get_arg(0);
            //if($arg instanceof Model or $arg instanceof EasyEntity or is_array($arg)){
            if(is_object($arg) or is_array($arg)){
                $dataModel = $arg;
            }
            else if(is_string($arg)){
                $viewPath = $arg;
            }
            else if($arg instanceof ViewData){
                $viewData = $arg;
            }
            unset($arg);
            break;
        case 2:
            //first argument is assumed to be view path
            $viewPath = func_get_arg(0);
            //second argument is assumed to be an object of either Entity or a Model class
            // or simply an object of ViewData   
            $arg2 = func_get_arg(1);
            if(is_object($arg2) or is_array($arg2)){
                $dataModel = $arg2;
            }
            else if($arg2 instanceof ViewData){
                $viewData = $arg2;
            }
            unset($arg2);            
            break;
        default:
            //first argument is view path
            $viewPath = func_get_arg(0);
            //second argument is assumed to be an object of either Entity or a Model class
            $dataModel = func_get_arg(1);
            //third argument is the view data
            $viewData = func_get_arg(2);       
            
    }
    if($viewPath !== ""){        
        if(file_exists(VIEWS_PATH.$viewPath.'.view.php') && is_readable(VIEWS_PATH.$viewPath.'.view.php')){
            $viewPath = VIEWS_PATH.$viewPath.'.view.php';
        }
        else if(file_exists(VIEWS_PATH."Shared".DS.$viewPath.'.view.php') && is_readable(VIEWS_PATH."Shared".DS.$viewPath.'.view.php')){
            $viewPath = VIEWS_PATH."Shared".DS.$viewPath.'.view.php';
        }
        else if($controllerObj instanceof Controller){
            $controller_name = $router->getController();
            $viewPath = VIEWS_PATH.$controller_name.DS.$viewPath.'.view.php';
        }
    }
    $view_obj = new View($viewPath,$viewData);
    if(!is_null($dataModel)){
        $view_obj->setDataModel($dataModel);
    }
    $viewData->content = $view_obj->render();
    $layout = Config::get('default_view_container');
    //Finding container view
    if(file_exists(VIEWS_PATH.$layout.'.view.php')){
        $layout_path = VIEWS_PATH.$layout.'.view.php';
    }
    else if(file_exists(VIEWS_PATH."Shared".DS.$layout.'.view.php')){
        $layout_path = VIEWS_PATH."Shared".DS.$layout.'.view.php';
    }
    else{
        $layout_path="";
    }
    $layout_view_obj = new View($layout_path,$viewData);
    return $layout_view_obj; //return the whole view object with view container
}

//function to return a view that shows error details when any error occurs
function errorView($httpCode,$errorMessage="",$errorDetails="",bool $isPartial = false) : View{
    
    $viewData = new ViewData();
    $viewData->httpCode = $httpCode;
    $viewData->httpStatus = HttpStatus::getStatus($httpCode);
    $viewData->ErrorMessage = $errorMessage;
    $viewData->ErrorDetail = $errorDetails;
    //path to error page
    $path = VIEWS_PATH."Shared".DS."error.view.php";
    $view = new View($path,$viewData);
    if($isPartial  == true){
        return $view;
    }
    
    $viewData->content = $view->render();
    
    $layout = Config::get('default_view_container');//$this->router->getRoute();
    //$layout_path = VIEWS_PATH."Shared".DS.$layout.'.view.php';
    //Finding container view
    if(file_exists(VIEWS_PATH.$layout.'.view.php')){
        $layout_path = VIEWS_PATH.$layout.'.view.php';
    }
    else if(file_exists(VIEWS_PATH."Shared".DS.$layout.'.view.php')){
        $layout_path = VIEWS_PATH."Shared".DS.$layout.'.view.php';
    }
    else{
        $layout_path="";
    }
    $layout_view_obj = new View($layout_path,$viewData);
    return $layout_view_obj;
}
/*************************************-------*****************************************/


/*---------------------------- ACCOUNTS RELATED FUNCTIONS -----------------------*/

//function to get roles assigned to a user 
function getRoles(string $userId):array{
    $em = new MyEasyPHP\Libs\EasyEntityManager();
    $list = $em->readTable("UserRoles UR", ["UR.RoleId","R.Name as role_name"])
                    ->leftJoin("Roles R")->on("UR.RoleId = R.Id")->where([
                        "UR.UserId"=>$userId
                    ])->get();
    $userRoles = [];
    if(!is_null($list)){
        foreach ($list as $row){
            $userRoles[] = strtolower($row['role_name']);
        }
    }
    return $userRoles;
}
//function to check whether a user's role is the input user id and role name
function isRole($userId,$role_name):bool{
    if(is_null($role_name) || trim($role_name)==""){
        return false;
    }
    $roles = getRoles($userId);
    if(sizeof($roles)==0){
        return false;
    }
    if(in_array(strtolower($role_name), $roles)){
        return true;
    }
    return false;
}
/*********************------------**********************************/

/*----------------------- ERROR HANDLING FUNCTION -------------------------*/
function handleMyEasyPHPError($errNo, $errMsg, $errFile, $errLine,$errTypes) {
    if($errNo!=E_NOTICE){
        http_response_code(500);
        $errDetails = "**Please check line no. ".$errLine." of the file ".$errFile;
        $view = errorView(500, "[{$errNo}]".$errMsg.'.',$errDetails);
        echo $view->render();
        exit();
    }
}

