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
use MyEasyPHP\Libs\Response;

class LoginViewModel extends Model{
    //put your code here
    
    public $Email;
    public $Password;

    public function isValidModel(): Response
    {
        $response = new Response;
        $response->set([
            "status"=>true,
            "status_code"=>200,
            "msg"=>""
        ]);
        if(is_null($this->Email) || trim($this->Email)==""){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Missing your email."
            ]);
        }
        else if(!filter_var($this->Email, FILTER_VALIDATE_EMAIL)){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Your email is invalid."
            ]);
        }
        else if(is_null($this->Password) || trim($this->Password)==""){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Your password is empty."
            ]);
        }
        $response->data = $this;
        return $response;
    }
}
