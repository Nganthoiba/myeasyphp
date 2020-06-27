<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;

/**
 * Description of Model
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Response;
use Exception;
class Model {
    /*Convert self object to array*/
    public function toArray(){
        return json_decode(json_encode($this),true);
    }
    
    /*** method to set data to a model ***/
    public function setModelData(array $data){
        $obj_data = $this->toArray();
        $flag=0;
        $form_element="";
        foreach($obj_data as $key=>$val){
            //$this->{$key} = isset($data[$key])?$data[$key]:null;
            if(isset($data[$key])){
                $this->{$key} = $data[$key];
            }
            else{
                $flag = 1;
                $form_element = $key;
                break;
            }
        }
        if($flag){
            throw new Exception("Could not find an element with name '".$form_element."' in your input request form.".json_encode($data),
                    400);            
        }
    }
    
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
            /*
            throw new Exception("Found null for the element with name '".$form_element."' in your input request form.",
                    400);  
                    */
            $response->msg = "Found null or blank value for the element with name '".$form_element."' in your input request form.";
            $response->status= false;
            $response->status_code = 400;          
        }
        $response->data = $this;
        return  $response;
    }   
    
}
