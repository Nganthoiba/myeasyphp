<?php
namespace MyEasyPHP\Models;

/**
 * Description of ContactModel
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Validation;
class ContactModel extends Model{
    public $Name;
    public $Email;
    public $Body; 
    public $Sex; 
    
    public function rules(): array {
        return [
            'Name' => [Validation::RULE_REQUIRED],
            'Email' => [Validation::RULE_REQUIRED, Validation::RULE_EMAIL],
            'Body' => [Validation::RULE_REQUIRED]
        ];
    }
}
