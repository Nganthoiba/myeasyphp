<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;

/**
 * Description of Model base class
 * 
 * @author Nganthoiba
 */
use ReflectionClass;
use ReflectionProperty;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
use MyEasyPHP\Libs\Attributes\Display;

class Model {
       
    protected $errors = [];//set of error for different attributes
    protected $propertyDisplayNames = [];//How data members (properties) will be displayed in the View File (Form)
    protected $reflectionclass;//reflection class
    protected $reflectionProperties = [];//properties in the class only for public
    
    
    public function __construct() {
        $this->reflectionclass = new \ReflectionClass($this);
        $this->reflectionProperties = $this->reflectionclass->getProperties(ReflectionProperty::IS_PUBLIC);
        $this->setPropertyDisplayNames();
    }
    /*Convert self object to array*/
    public function toArray(){
        return json_decode(json_encode($this),true);
    }
    
    /*** method to set data to a model ***/
    public function setModelData(array $data) {       
        
        foreach ($this->reflectionProperties as $property){
            $propertyName = $property->getName();
            $dataValue = $this->getDataValue($data, $propertyName);            
            if($dataValue === "NOT_EXIST"){  
                $this->setDefaultValue($property);
                continue;
            } 
            switch ($property->getType()){
                case 'int':
                    $this->{$propertyName} = intval($dataValue);//(int)($data[$propertyName]);
                    break;
                case 'float':
                    $this->{$propertyName} = floatval($dataValue);//$data[$propertyName];
                    break;
                case 'bool':
                    $this->{$propertyName} = (strtolower($dataValue)==='true')?true:false;
                    break;
                default:                    
                    $this->{$propertyName} = $dataValue;
            }
            
        }
        
    }
    
    private function getDataValue(array $data, $key){
        if(is_integer($key)){
            return $data[$key]??"NOT_EXIST";
        }
        if(isset($data[$key])){
            return $data[$key];
        }
        $keys = array_keys($data);
        foreach($keys as $k){
            if(strtolower($k) === strtolower($key)){
                return $data[$k];
            }
        }
        return "NOT_EXIST";//means data doest not exist
    }
    
    public function isValidated():bool{
        foreach ($this->reflectionProperties as $property){
            foreach($property->getAttributes() as $attribute){ 
                $validator = $attribute->newInstance();
                if($validator instanceof Validator){
                    $validator->validate($this,$property->getName());
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
    
    //Method to read and set display names for all the public properties in the class
    protected function setPropertyDisplayNames(){
        $reflectionClass = new ReflectionClass($this);
        $memberData = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($memberData as $property){
            foreach($property->getAttributes(Display::class) as $attribute){                
                $this->addPropertyDisplayName($property->getName(), 
                        $attribute->newInstance()->Name);  
            }
        }
    }  
    
    //method to set default value when property is not initialised
    protected function setDefaultValue($property){
        if($property->isInitialized($this)){
            return;
        }
        switch ($property->getType()){
            case 'int':
                $property->setValue($this,0);//(int)($data[$propertyName]);
                break;
            case 'float':
                $property->setValue($this,0.0);//$data[$propertyName];
                break;
            case 'bool':
                $property->setValue($this,false);
                break;
            case 'string':
                $property->setValue($this,'');
                break;
            default:                    
                $property->setValue($this,null);
        }
    }
    
}
