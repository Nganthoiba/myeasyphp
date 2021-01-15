<?php
namespace MyEasyPHP\Libs\Attributes\Validations;
use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
/**
 * Description of Required
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Required extends Validator{
    public function __construct(?string $ErrorMessage = null) {
        $this->ErrorMessage = $ErrorMessage;
    }
    public function __invoke(\MyEasyPHP\Libs\Model $object,string $property){       
        if(is_null($object->{$property}) || trim($object->{$property}) === ""){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' is required.';
            $object->addError($property, $message);
        }
    }
}
