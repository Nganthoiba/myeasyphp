<?php
namespace MyEasyPHP\Libs\Attributes\Validations;
use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
use DateTime;
/**
 * Description of Date
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Date extends Validator{
    public string $Format;
    public function __construct(string $Format = 'd-m-Y', ?string $ErrorMessage = null) {
        $this->Format = $Format;
        $this->ErrorMessage = $ErrorMessage;
    }
    
    public function validate(\MyEasyPHP\Libs\Model $object, string $property) {
        $date = $object->{$property};
        if($this->validateDate($date, $this->Format) === false){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' is not a valid date format. '
                    . 'Date must be in the format '.$this->Format;
            $object->addError($property, $message);
        }
    }
    private function validateDate($date, $format = 'd-m-Y'){
        $dt = DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }
}
