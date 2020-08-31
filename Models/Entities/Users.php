<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models\Entities;

/**
 * Description of Users
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\EasyEntity as Entity;
class Users extends Entity{
    /*** data structures for user***/
    public  $user_id , 
            $full_name ,     
            $email  ,        
            $phone_number  ,     
            $role_id,        
            $user_password, 
            $security_stamp,//salt
            $verify,//email verification         
            $create_at,     
            $update_at,      
            $delete_at,      
            $profile_image,  
            $aadhaar,        
            $update_by;
    public function __construct() {
        parent::__construct();
        $this->setKey("user_id");
    }
}
