<?php
namespace MyEasyPHP\Libs\Attributes\Validations;

/**
 * Description of Email
 *
 * @author Nganthoiba
 */
use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
#[Attribute(Attribute::TARGET_PROPERTY)]
class Email extends Validator{
    public function __construct(?string $ErrorMessage = null) {
        $this->ErrorMessage = $ErrorMessage;
    }
    public function validate(\MyEasyPHP\Libs\Model $object,string $property){       
        if(filter_var($object->{$property}, FILTER_VALIDATE_EMAIL) === false){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' should be a valid email address.';
            $object->addError($property, $message);
        }
    }
}
