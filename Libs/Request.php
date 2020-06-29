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
use Exception;
class Request {
    //put your code here
    private $method; //HTTP methods (verbs): GET, POST, PUT, DELETE
    private $header; //HTTP request header
    private $content_type; //Content type
    private $source; //source of the request(client IP)
    private $device;
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
        $this->header = apache_request_headers();
        $this->content_type = get_data_from_array("Content-Type",$this->header);
        $this->source = get_client_ip();
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
                    //$data = $_POST;
                    foreach($_POST as $key => $value)
                    {
                      $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
                break;
            case "GET":
            case "DELETE":
                $data = $_GET;
                break;
            case "PUT":
                if($this->content_type === "application/json"){
                    $data = json_decode(file_get_contents("php://input"),true);
                }
                else{
                    $data = file_get_contents("php://input");
                }
                break;
        }
        //filtering and senitizing whatever input data is accepted
        $data = $this->senitizeInputs($data);
        return $data;
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
    
    public function getRequestHeaders(){
        return apache_request_headers();
    }
    
    public function getDevice(){
        return $this->device;
    }
    
    public function senitizeInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->senitizeInputs($v);
            }
        } else {
            if(is_null($data)){
                $clean_input = "";
            }
            else{
                $clean_input = trim(htmlspecialchars(strip_tags($data)));
            }
        }
        return $clean_input;
    }
}
