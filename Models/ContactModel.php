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
use MyEasyPHP\Libs\Attributes\Validations\Pattern;

class ContactModel extends Model{
    #[Display(Name: 'Your Name')]
    #[Required(ErrorMessage:'You must enter your name')]
    public $Name;
    
    #[Display(Name: 'Your Email')]  
    #[Required]
    #[Email(ErrorMessage:'Enter your email properly')]
    public $Email;
    
    #[Display(Name: 'Body Content')]
    #[Required]
    #[Minlength(size:35,ErrorMessage:'Characters 35 tagi taba yade')]
    public $Body; 
    #[Required('Please choose an option')]
    public $Sex;
}
