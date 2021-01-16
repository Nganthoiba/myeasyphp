<?php
namespace MyEasyPHP\Libs\Attributes\Validations;
use Attribute;
/**
 * Description of Validator
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Validator {
    public string|null $ErrorMessage;    
    public function __invoke(\MyEasyPHP\Libs\Model $object,string $property){       
        /***
         * Implement your own validation in your derived class
        if(anything unwanted happens){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' anything you want to say.';
            $object->addError($property, $message);
        }
         */
    }
}
