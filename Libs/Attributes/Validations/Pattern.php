<?php
namespace MyEasyPHP\Libs\Attributes\Validations;

/**
 * Description of Pattern
 * For Matching a pattern
 * @author Nganthoiba
 */
use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
#[Attribute(Attribute::TARGET_PROPERTY)]
class Pattern extends Validator{
    public string $Pattern;
    /* pattern is the regular expression */
    public function __construct(string $Pattern, ?string $ErrorMessage = null) {
        $this->Pattern = $Pattern;
        $this->ErrorMessage = $ErrorMessage;
    }
    public function validate(\MyEasyPHP\Libs\Model $object,string $property){
        if(preg_match($this->Pattern,$object->{$property})===false){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' pattern not match.';
            $object->addError($property, $message);
        }
    }
}
