<?php
declare(strict_types=1);
/**
 * Description of Request
 * Basic structure of a request data
 * @author Nganthoiba
 * files used: 
 * 1.   libs/special_functions.php, 
 *      used function:  get_data_from_array()
 *                      get_client_ip()
 */
namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\Config;
use Exception;
class Request {
    //put your code here
    private $method; //HTTP methods (verbs): GET, POST, PUT/PATCH, DELETE
    private $header; //HTTP request header
    private $content_type; //Content type
    private $source; //source of the request(client IP)
    private $device; //User agent
    public function __construct() {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);//getting HTTP Verb
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
        $this->header = $this->getRequestHeaders();
        $this->content_type = $_SERVER['CONTENT_TYPE']??"";//get_data_from_array("Content-Type",$this->header);
        $this->source = get_client_ip();//defined in special_functions.php
        $this->device = filter_input(INPUT_SERVER,'HTTP_USER_AGENT');
    }
    //method to get data sent from client
    public function getData(){
        $data = array();
        switch ($this->method){
            case "POST":
                if($this->content_type === "application/json"){
                    $data = json_decode(file_get_contents("php://input"),true);
                }
                else{
                    $data = $this->filterSpecialChars($_POST,'POST');                    
                }
                break;
            case "GET":
            case "DELETE":
                $data = $this->filterSpecialChars($_GET,'GET');//$_GET;
                unset($data['uri']);
                break;            
            case "PUT":
            case "PATCH":
                if($this->content_type === "application/json"){
                    $data = json_decode(file_get_contents("php://input"),true);
                }
                else if($this->content_type == "application/x-www-form-urlencoded"){
                    parse_str(file_get_contents("php://input"), $data);             
                    $data = json_decode(json_encode($data),true);
                }
                else if(strpos($this->content_type,"multipart/form-data") !== false){
                    $lines = file('php://input');
                    foreach($lines as $i =>  $line){
                        $search = 'Content-Disposition: form-data; name=';
                        if(strpos($line, $search) !== false){                            
                            $key = str_replace($search,"",preg_replace("/[\r,\n,\"]/","",$line));
                            $data[$key] = trim($lines[$i+2]);
                        }
                    }                    
                }
                break;
        }
        //filtering and senitizing whatever input data is accepted
        return $this->cleanInputs($data);
    }
    
    //method to check if request method is allowed
    public function isMethod($verbs = array()){
        if(is_string($verbs)){
            return (trim($verbs) === $this->method);
        }
        if(is_array($verbs)){
            return (in_array($this->method, $verbs));
        }
        return false;
    }
    
    public function getMethod(){
        return $this->method;
    }
    
    public function getSourceIP(){
        return $this->source;
    }
    
//    public function getRequestHeaders(){
//        return apache_request_headers();
//    }
    
    function getRequestHeaders() {
        $headers = array();
        $headers['Content-Type'] = $_SERVER['CONTENT_TYPE']??"";
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) !== 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;            
        }
        return $headers;
    }
    //returns domain
    public function getHost(){
        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
        $protocol = ($isHttps)?"https://":"http://";
        $headers = $this->getRequestHeaders();
        return isset($headers['Host'])?$protocol.$headers['Host'].Config::get('host'):null;
    }
    
    public function getDevice(){
        return $this->device;
    }
    
    /*
     * Method for removing special characters
     */
    public function filterSpecialChars(array $data=array(),string $method = 'GET'){
        if(sizeof($data) == 0){
            return $data;
        }
        foreach($data as $key => $value)
        {
            if(is_array($value)){
                $data[$key] = $this->filterSpecialChars($value,$method);
            }
            else if(!is_numeric($key)){                    
                $type = ($method=="POST")?INPUT_POST:INPUT_GET;
                $string = filter_input($type, $key, FILTER_SANITIZE_SPECIAL_CHARS);                  
                //removing special characters
                $data[$key] = $string;            
            }
        }
        return $data;
    }
    
    /* Method for recursively removing risky elements to avoid Cross Site Scripting (xss)*/
    public function cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->cleanInputs($v);
            }
        } else {
            if(is_null($data)){
                $clean_input = "";
            }
            else{   
                $string = filter_var("".$data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                $clean_input = trim(htmlspecialchars(strip_tags(stripslashes($string)),ENT_QUOTES, 'UTF-8'));           
            }
        }
        return $clean_input;
    }
    
    public static function getURI(){
        return filter_input(INPUT_GET, "uri", FILTER_SANITIZE_SPECIAL_CHARS);
    }
}
