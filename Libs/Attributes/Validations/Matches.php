<?php
namespace MyEasyPHP\Libs\Attributes\Validations;

/**
 * Description of Match
 * To be used only when two properties of a class are compared.
 * e.g. password and confirmation password
 * @author Nganthoiba
 */
use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
#[Attribute(Attribute::TARGET_PROPERTY)]
class Matches extends Validator {
    public string $PropertyName;//name of the property to be compared with
    public function __construct(string $PropertyName, ?string $ErrorMessage = null ) {
        $this->PropertyName = $PropertyName;
        $this->ErrorMessage = $ErrorMessage;
    }
    public function validate(\MyEasyPHP\Libs\Model $object, string $property) {
        if($object->{$this->PropertyName} !== $object->{$property}){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' does not match with '. $object->getPropertyDisplayName($this->PropertyName);
            $object->addError($property, $message);
        }
    }
}
