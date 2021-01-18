<?php
namespace MyEasyPHP\Models;

/**
 * Description of ContactModel
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Attributes\Display;
use MyEasyPHP\Libs\Attributes\Validations\Email;
use MyEasyPHP\Libs\Attributes\Validations\Minlength;
use MyEasyPHP\Libs\Attributes\Validations\Required;
class ContactModel extends Model{
    #[Display(Name: 'Your Name')]
    #[Required]
    public $Name;
    
    #[Display(Name: 'Your Email')]  
    #[Required]
    #[Email]
    public $Email;
    
    #[Display(Name: 'Body Content')]
    #[Required]
    #[Minlength(Size:35)]
    public $Body; 
    #[Required('Please choose an option')]
    public string $Sex;
}
