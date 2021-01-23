<?php
declare(strict_types=1);
/**
 * Description of response:
 * Response class defines the basic data structure to respond an information or message to the client
 
 * data:        It is the additional information that will be responded
 * error:       It is the error message sent to the client if status is false, otherwise its value is null
 * status_code: It is the HTTP status code
 * status:      It is a flag of either true or false. If it is true, it means the request has been 
    *           fulfilled successfully (with no error)
 * msg:         It is a message to the client.
 * 
 * @author Nganthoiba
 */
namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\HttpStatus;
use MyEasyPHP\Libs\XML\XMLEncoder;
class Response {
    private $contentType;
    private $httpVersion;
    //data members compulsory for this response class
    public $data,$error,$status,$status_code,$msg,$sqlErrorCode;
    public function __construct() {
        //By default
        $this->data = null;
        $this->error = null;
        $this->status = false;
        $this->status_code = 0;//http status code
        $this->sqlErrorCode=0;
        $this->msg = "";
        $this->httpVersion = "HTTP/1.1";
    }
    public function set(array $data = []){
        foreach($data as $key=>$val){
            $this->{$key} = $val;
        }
        return $this;
    }
    public function setContentType($contentType){
        $this->contentType = $contentType;
    }
    public function setHttpVersion($httpVersion){
        $this->httpVersion = $httpVersion;
    }
    
    public function toJSON(){
        header("Content-Type: application/json");
        header($this->httpVersion.' '. $this->status_code . ' ' . HttpStatus::getStatus($this->status_code));
        if(is_null($this->msg) || $this->msg == ""){
            $this->msg = HttpStatus::getStatus($this->status_code);
        }
        http_response_code($this->status_code);
        return json_encode($this);
    }
    public function toXML(){
        header("Content-Type: application/xml");
        header($this->httpVersion.' '. $this->status_code . ' ' . HttpStatus::getStatus($this->status_code));
        if(is_null($this->msg) || $this->msg == ""){
            $this->msg = HttpStatus::getStatus($this->status_code);
        }
        http_response_code($this->status_code);
        return XMLEncoder::encode($this);
    }
    
    
}
