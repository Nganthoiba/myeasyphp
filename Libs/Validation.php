<?php
namespace MyEasyPHP\Libs;

/**
 * Description of Validation:-
 * This class is meant for storing set of validation constants and rules that can be applied
 * on an attribute.
 *
 * @author Nganthoiba
 */
class Validation {
    //Validation constants
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_NUMERIC = 'numeric';
    public const RULE_MATCH = 'match';
    
    public static function errorMessages(){
        return [
            self::RULE_REQUIRED => "The field is required.",
            self::RULE_EMAIL => "The field must be a valid email address.",
            self::RULE_MIN => "The field must be at least {min} characters long.",
            self::RULE_MAX => "The field must be at most {min} characters long.",
            self::RULE_MATCH => "The field must be the same as {match}."
        ];
    }
}
