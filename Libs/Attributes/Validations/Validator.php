<?php
namespace MyEasyPHP\Libs\Attributes\Validations;
use Attribute;
/**
 * Description of Validator
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Validator {
    public string|null $ErrorMessage;
    public function __invoke(\MyEasyPHP\Libs\Model $object,string $property){
        
    }
}
