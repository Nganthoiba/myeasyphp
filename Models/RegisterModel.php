<?php
declare(strict_types=1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models;

/**
 * Description of RegisterModel
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Response;

class RegisterModel extends Model{
    public $Email,
            $Password,
            $Confirm_password,
            $PhoneNumber,
            $UserName;//user full name
    public function __construct() {
        $this->Email="";
        $this->Password="";
        $this->Confirm_password="";
        $this->PhoneNumber="";
        $this->UserName="";
    }

    public function setModelData(array $data) {
        parent::setModelData($data);
    }
    //Overriding model validation
    public function isValidModel() : Response{
        $response = new Response();
        $response->set([
            "status"=>true,
            "status_code"=>200,
            "msg"=>""
        ]);

        if(is_null($this->Email) || $this->Email == ""){
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
        else if(is_null($this->UserName) || $this->UserName == ""){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Missing your user name."
            ]);
        }
        else if(is_null($this->PhoneNumber) || $this->PhoneNumber == ""){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Missing your phone number."
            ]);
        }
        else if(is_null($this->Password) || $this->Password == ""){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Missing your password."
            ]);
        }
        else if(is_null($this->Confirm_password) || $this->Confirm_password == ""){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Missing your confirmation password."
            ]);
        }
        else if($this->Password !== $this->Confirm_password){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Your confirmation password does not match your password. Please retype your passwords."
            ]);
        }
        
        return $response;
    }
}
