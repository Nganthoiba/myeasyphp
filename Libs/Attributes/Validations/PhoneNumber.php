<?php
namespace MyEasyPHP\Libs\Attributes\Validations;

use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
/**
 * Description of PhoneNumber
 * To validate phone number
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class PhoneNumber extends Validator{    
    public function validate(\MyEasyPHP\Libs\Model $object,string $property){       
        if($this->isValidPhoneNumber($object->{$property})===false){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' is not a valid phone number.';
            $object->addError($property, $message);
        }
    }
    private function isValidPhoneNumber($phone):bool
    {
        if(!preg_match("/^([0-9]+)$/", $phone)){
            return false;
        }
        // Allow +, - and . in phone number
        $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
        // Remove "-" from number
        $phone_to_check = str_replace("-", "", $filtered_phone_number);

        // Check the lenght of number
        // This can be customized if you want phone number from a specific country
        if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) {
            return false;
        } 
        return true;
    }
}
