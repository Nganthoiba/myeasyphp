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
class Response {
    //data members compulsory for this response class
    public $data,$error,$status,$status_code,$msg,$sqlErrorCode;
    public function __construct() {
        //By default
        $this->data = null;
        $this->error = null;
        $this->status = false;
        $this->status_code = 0;
        $this->sqlErrorCode=0;
        $this->msg = "";
    }
    public function set(array $data = []){
        /*
        $this->data = isset($data['data'])?$data['data']:$this->data;
        $this->error = isset($data['error'])?$data['error']:$this->error;
        $this->status = isset($data['status'])?$data['status']:$this->status;
        $this->status_code = isset($data['status_code'])?$data['status_code']:$this->status_code;
        $this->msg = isset($data['msg'])?$data['msg']:$this->msg;
        */
        foreach($data as $key=>$val){
            $this->{$key} = $val;
        }
        return $this;
    }
}
