<?php
declare(strict_types=1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models;

/**
 * Description of UserViewModel
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Models\Entities\Users;
use MyEasyPHP\Libs\Response;

class UserViewModel extends Model{
    public  $Id,
            $Email,
            $PhoneNumber,
            $UserName;
    public function __construct(Users $user) {
        $this->Id = $user->Id;
        $this->Email = $user->Email;
        $this->PhoneNumber = $user->PhoneNumber;
        $this->UserName = $user->UserName;
    }
    
    public function isValidModel(): Response {
        //parent::isValidModel();
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
        else if(!$this->isValidPersonName($this->UserName)){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"User Name is invalid, please check."
            ]);
        }
        else if(is_null($this->PhoneNumber) || $this->PhoneNumber == ""){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"Missing your phone number."
            ]);
        }
        else if(!$this->isValidPhoneNumber($this->PhoneNumber)){
            $response->set([
                "status"=>false,
                "status_code"=>400,
                "msg"=>"You have entered invalid phone number."
            ]);
        }
        return $response;
    }
    
    private function isValidPhoneNumber($phone):bool
    {
        if(!preg_match("/^([0-9]+)$/", $phone)){
            return false;
        }
        // Allow +, - and . in phone number
        $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
        // Remove "-" from number
        $phone_to_check = str_replace("-", "", $filtered_phone_number);

        // Check the lenght of number
        // This can be customized if you want phone number from a specific country
        if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) {
            return false;
        } else {
            return true;
        }
    }
    private function isValidPersonName(string $name){
        if($name == ""){
            return false;
        }
        return preg_match("/^([a-zA-Z'. ]+)$/",$name);            
    }
}
