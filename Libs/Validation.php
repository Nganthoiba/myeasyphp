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
    public const RULE_MIN = 'minLength';
    public const RULE_MAX = 'maxLength';
    public const RULE_NUMERIC = 'numeric';
    public const RULE_MATCH = 'match';
    public const RULE_ALPHA = 'alpha';
    public const RULE_DATE = 'date';
    
    public static $errorMessages= [
            self::RULE_REQUIRED => "The field is required.",
            self::RULE_EMAIL => "The field must be a valid email address.",
            self::RULE_MIN => "The field must be at least {min} characters long.",
            self::RULE_MAX => "The field must be at most {min} characters long.",
            self::RULE_NUMERIC => "The field must be only numbers.",
            self::RULE_MATCH => "The field must be the same as {match}.",
            self::RULE_ALPHA => "The field must be of only alphabet characters.",
            self::RULE_DATE => "The field must be a valid date."
        ];
    
    
    private $_errors = [];


    public function validate($src, $rules = [] ){

        foreach($src as $item => $item_value){
            if(key_exists($item, $rules)){
                foreach($rules[$item] as $rule => $rule_value){

                    if(is_int($rule))
                         $rule = $rule_value;

                    switch ($rule){
                        case self::RULE_REQUIRED:
                            if(empty($item_value) && $rule_value){
                                $this->addError($item,ucwords($item). ' required');
                            }
                        break;

                        case self::RULE_MIN:
                            if(strlen($item_value) < $rule_value){
                                $this->addError($item, ucwords($item). ' should be minimum '.$rule_value. ' characters');
                            }       
                        break;

                        case self::RULE_MAX:
                            if(strlen($item_value) > $rule_value){
                                $this->addError($item, ucwords($item). ' should be maximum '.$rule_value. ' characters');
                            }
                        break;

                        case self::RULE_NUMERIC:
                            if(!ctype_digit($item_value) && $rule_value){
                                $this->addError($item, ucwords($item). ' should be numeric');
                            }
                        break;
                        case self::RULE_ALPHA:
                            if(!ctype_alpha($item_value) && $rule_value){
                                $this->addError($item, ucwords($item). ' should be alphabetic characters');
                            }
                        default :
                    }
                }
            }
        }    
    }

    private function addError($item, $error){
        $this->_errors[$item][] = $error;
    }


    public function error(){
        if(empty($this->_errors)) return false;
        return $this->_errors;
    }
    /*
     * HOW TO USE:
     * $data = ['username' => '', 'password' => 'pass'];
        $rules = [
            'username' => ['required', 'minLen' => 6,'maxLen' => 150, 'alpha'],
            'password' => ['required', 'minLen' => 8]
        ];
        $v = new Validation();
        $v->validate($data, $rules);
        if($v->error()){
            print_r($v->error());
        } else{
            echo 'Ok';
        }

     */
}
