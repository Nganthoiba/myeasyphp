<?php
namespace MyEasyPHP\Libs\Attributes\Validations;

use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
/**
 * Description of Minlength
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Minlength extends Validator{
    public int $Size;
    public function __construct(int $Size, ?string $ErrorMessage=null) {
        $this->Size = $Size;
        $this->ErrorMessage = $ErrorMessage;
    }
    
    public function validate(\MyEasyPHP\Libs\Model $object,string $property){
        if(strlen($object->{$property}) < $this->Size){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' should be minimum of '. $this->Size.' characters length';
            $object->addError($property, $message);
        }
    }
}
