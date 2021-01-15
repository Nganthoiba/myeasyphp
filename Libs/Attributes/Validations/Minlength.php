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
    public int $size;
    public function __construct(int $size, ?string $ErrorMessage=null) {
        $this->size = $size;
        $this->ErrorMessage = $ErrorMessage;
    }
    
    public function __invoke(\MyEasyPHP\Libs\Model $object,string $property){
        if(strlen($object->{$property}) < $this->size){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' should be minimum of '. $this->size.' characters length';
            $object->addError($property, $message);
        }
    }
}
