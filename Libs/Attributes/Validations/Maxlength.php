<?php
namespace MyEasyPHP\Libs\Attributes\Validations;
use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
/**
 * Description of Maxlength
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Maxlength extends Validator{
    public int $Size;
    public function __construct(int $Size, ?string $ErrorMessage = null) {
        $this->Size = $Size;
        $this->ErrorMessage = $ErrorMessage;
    }
    public function validate(\MyEasyPHP\Libs\Model $object,string $property){
        if(strlen($object->{$property}) > $this->Size){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' should not be more than '. $this->Size.' characters length';
            $object->addError($property, $message);
        }
    }
}
