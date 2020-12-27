<?php
namespace MyEasyPHP\Models;

/**
 * Description of ContactModel
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
class ContactModel extends Model{
    public $Name;
    public $Email;
    public $Body; 
    
    public function rules(): array {
        return [
            'Name' => [self::RULE_REQUIRED],
            'Email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'Body' => [self::RULE_REQUIRED]
        ];
    }
}
