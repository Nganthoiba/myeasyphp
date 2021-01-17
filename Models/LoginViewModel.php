<?php
declare(strict_types=1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models;

/**
 * Description of AccountViewModels
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Attributes\Display;
use MyEasyPHP\Libs\Attributes\Validations\Email;
use MyEasyPHP\Libs\Attributes\Validations\Required;

class LoginViewModel extends Model{
    //put your code here
    #[Display(Name:'Your email')]
    #[Required]
    #[Email]
    public $Email;
    
    #[Display(Name:'Your password')]
    #[Required]
    public $Password;
    
}
