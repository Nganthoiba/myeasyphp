<?php
namespace MyEasyPHP\Libs\Attributes\Validations;
use Attribute;
use MyEasyPHP\Libs\Attributes\Validations\Validator;
/**
 * Description of Between
 * Between checks whether an input numbers lies in between given 
 * ranges upper bound and lower bound.
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Between extends Validator{
    public int|float $LowerBound, $UpperBound;
    public function __construct(int|float $LowerBound,int|float $UpperBound, ?string $ErrorMessage = null) {
        $this->LowerBound = $LowerBound;
        $this->UpperBound = $UpperBound;
        $this->ErrorMessage = $ErrorMessage;
    }
    public function validate(\MyEasyPHP\Libs\Model $object,string $property){
        
        //we need to make sure that the property must have integer or float value i.e. numeric
        if(is_numeric($object->{$property}) === false){
            $object->addError($property, $property.' must have numeric value.');
            return;
        }
        
        if(($object->{$property})<$this->LowerBound || ($object->{$property})>$this->UpperBound){
            $message = !is_null($this->ErrorMessage)?$this->ErrorMessage:$object->getPropertyDisplayName($property).' should be between '. $this->LowerBound.' and '.$this->UpperBound;
            $object->addError($property, $message);
        }
    }
}
