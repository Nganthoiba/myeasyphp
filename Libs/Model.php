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
use ReflectionClass;
use ReflectionProperty;

abstract class Model {
       
    protected $errors = [];//set of error for different attributes
    protected $propertyDisplayNames = [];//How data members (properties) will be displayed in the View File (Form)
    
    public function __construct() {
        $this->setDisplayName();
    }
    /*Convert self object to array*/
    public function toArray(){
        return json_decode(json_encode($this),true);
    }
    
    /*** method to set data to a model ***/
    public function setModelData(array $data) {        
        $reflectionClass = new ReflectionClass($this);
        $reflectionProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($reflectionProperties as $property){
            $propertyName = $property->getName();
            if(isset($data[$propertyName])){
                switch ($property->getType()){
                    case 'int':
                        $this->{$propertyName} = (int)($data[$propertyName]);
                        break;
                    case 'float':
                        $this->{$propertyName} = (float)$data[$propertyName];
                        break;
                    case 'bool':
                        $this->{$propertyName} = ($data[$propertyName]==='true')?true:false;
                        break;
                    default:
                        $this->{$propertyName} = $data[$propertyName];
                }
            }
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
    public function validate(){
        /*
        $validator = new Validation();
        $validator->validate($this, $this->rules());
        $this->errors = $validator->error();*/
        $reflectionClass = new ReflectionClass($this);
        $memberData = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($memberData as $property){
            foreach($property->getAttributes() as $attribute){ 
                $obj = $attribute->newInstance();
                if(is_callable($obj)){
                    $obj($this,$property->getName());
                }
            }
        }
        return empty($this->errors);
    }
    
    public function __toString(){
        return json_encode($this);
    }
    
    public function addPropertyDisplayName(string $property, string $message){
        $this->propertyDisplayNames[$property] = $message;
    }
    public function getPropertyDisplayName(string $property){
        return ($this->propertyDisplayNames[$property])??$property;
    }
    
    public function getAllDisplayNames(){
        return $this->propertyDisplayNames;
    }
    
    public function addError(string $property, string $message){
        $this->errors[$property][] = $message;
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
    public function __set($key, $value) {
        if(property_exists($this, $key)){
            $this->{$key} = $value;
        }
    }
    
    //Method to read and set display names for all the public properties in the class
    protected function setDisplayName(){
        $reflectionClass = new ReflectionClass($this);
        $memberData = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($memberData as $property){
            foreach($property->getAttributes() as $attribute){
                if(basename($attribute->getName())==="Display"){
                    $this->addPropertyDisplayName($property->getName(), $attribute->newInstance()->Name);                
                }
            }
        }
    }    
}
