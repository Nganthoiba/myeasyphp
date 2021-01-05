<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;

/**
 * Description of Model base class
 * 
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Response;
use MyEasyPHP\Libs\Validation;
abstract class Model {
       
    protected $errors = [];//set of error for different attributes
    /*Convert self object to array*/
    public function toArray(){
        return json_decode(json_encode($this),true);
    }
    
    /*** method to set data to a model ***/
    public function setModelData(array $data){
        foreach($data as $key=>$value){
            $this->{$key} = $value;
        }
    }
    //this function will be deprecated
    public function isValidModel(): Response{
        $response = new Response();
        $response->set([
            "status"=>true,
            "status_code"=>200,
            "msg"=>""
        ]);
        $obj_data = $this->toArray();
        $flag=0;
        $form_element="";
        foreach($obj_data as $key=>$val){
            if(is_null($val) || trim($val) === ""){                
                $flag = 1;
                $form_element = $key;
                break;
            }
        }
        if($flag){
            $response->msg = "Found null or blank value for the element with name '".$form_element."' in your input request form.";
            $response->status= false;
            $response->status_code = 400;          
        }
        $response->data = $this;
        return  $response;
    }   
    public function rules():array{
        //this method must be overridden in the based class
        return [];
    }
    public function validate(){
        $validator = new Validation();
        $validator->validate($this->toArray(), $this->rules());
        $this->errors = $validator->error();
        return empty($this->errors);
    }
    
    public function __toString(){
        return json_encode($this);
    }
    
    public function addError(string $attribute, string $message){
        $this->errors[$attribute][] = $message;
    }
    public function getError(string $attribute){
        return (isset($this->errors[$attribute]))?$this->errors[$attribute][0]:"";
    }
    public function getAllErrors():array{
        return $this->errors;
    }    
    //abstract public function rules():array;
    
    /*
     * For security reason, restriction is made from accessing non-existing
     * properties of the class. With this feature, any non-existing property 
     * can not be set dynamically.
    */ 
    public function __get($name) {
        if(!property_exists($this, $name)){
            return null;
        }
        return $this->{$name};
    }
    public function __set($name, $value) {
        if(property_exists($this, $name)){
            $this->{$name} = $value;
        }
    }
    
}
