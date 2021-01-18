<?php
namespace MyEasyPHP\Libs\Attributes\Validations;
use Attribute;
/**
 * Description of Validator
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
abstract class Validator {
    public string|null $ErrorMessage;  
    public function __construct(?string $ErrorMessage = null/*Input message*/){
        $this->ErrorMessage = $ErrorMessage;
    }
    abstract public function validate(\MyEasyPHP\Libs\Model $object,string $property);
    //{       
        /***
         * Implement your own validation in your derived class
        if(anything unwanted happens){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' anything you want to say.';
            $object->addError($property, $message);
        }
         */
    //}
}
