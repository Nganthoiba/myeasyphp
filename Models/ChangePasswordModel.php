<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models;

/**
 * Description of ChangePasswordModel
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Response;
class ChangePasswordModel extends Model{
    public $currentPassword,$newPassword, $confirmPassword;
    
    public function isValidModel(): Response {
        //parent::isValidModel();
        $response = new Response();
        $response->status = true;
        $response->status_code = 200;
        if(is_null($this->currentPassword) || trim($this->currentPassword) == ""){
            $response->set([
                "status" => false,
                "status_code" => 403,
                "msg" => "Please submit your current password."
            ]);
        }
        else if(is_null($this->newPassword) || trim($this->newPassword) == ""){
            $response->set([
                "status" => false,
                "status_code" => 403,
                "msg" => "Please submit your new password."
            ]);
        }
        else if(is_null($this->confirmPassword) || trim($this->confirmPassword) == ""){
            $response->set([
                "status" => false,
                "status_code" => 403,
                "msg" => "Please submit your confirmation password."
            ]);
        }
        else if($this->newPassword !== $this->confirmPassword){
            $response->set([
                "status" => false,
                "status_code" => 403,
                "msg" => "Your confirmation password does not match your new password. Please retype your confirmation password."
            ]);
        }
        
        return $response;
    }
}
