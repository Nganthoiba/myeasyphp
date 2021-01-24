<?php
declare(strict_types=1);
namespace MyEasyPHP\Models;

/**
 * Description of RegisterModel
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Response;

use MyEasyPHP\Libs\Attributes\Validations\Required;
use MyEasyPHP\Libs\Attributes\Validations\Numeric;
use MyEasyPHP\Libs\Attributes\Validations\Email;
use MyEasyPHP\Libs\Attributes\Validations\Matches;
use MyEasyPHP\Libs\Attributes\Validations\Maxlength;
use MyEasyPHP\Libs\Attributes\Validations\PhoneNumber;
use MyEasyPHP\Libs\Attributes\Display;


class RegisterModel extends Model{
    #[Display(Name:'Your email')]
    #[Required]
    #[Email]
    public $Email;
    
    #[Display(Name:'Your password')]
    #[Required]
    public $Password;
    
    #[Display(Name:'Confirm password')]
    #[Required]
    #[Matches(PropertyName: 'Password')]
    public $ConfirmPassword;
    
    #[Display(Name:'Your contact number')]
    #[Required]
    #[PhoneNumber]
    public $PhoneNumber;
    
    #[Display(Name:'Your full name')]
    #[Required]
    public $UserName;//user full name   
    
}
