<?php
declare(strict_types=1);

namespace MyEasyPHP\Models;

/**
 * Description of LoginModel
 * Manages login information
 * @author Nganthoiba
 */
use MyEasyPHP\Models\Entities\Users;
use MyEasyPHP\Models\UserViewModel;
use MyEasyPHP\Models\Entities\Logins;
class LoginModel {
    public $login_id;
    public $Id;//user id
    public $UserName;
    public $Email;
    public $time; //time of login

    public function __construct(Users $user) {
        $this->login_id = generateRandomString(60);
        $this->Id = $user->user_id;
        $this->UserName = $user->full_name;
        $this->Email = $user->email;
        $this->time = time();
    }
    
    public static function isAuthenticated(){
        if(isset($_COOKIE['login_information']) && trim($_COOKIE['login_information'])!==""){
            //further need to check at server side also
            $loginInfo = json_decode($_COOKIE['login_information']);
            $login = new Logins();
            $currentDatetime = date('Y-m-d H:i:s');
            $login = $login->find([
                "login_id" => $loginInfo->login_id,
                "logout_time"=>['IS','NULL'],
                "expiry" => ['>',$currentDatetime]
            ]);            
            return (!is_null($login));
        }
        return false;
    }
    
    public static function getLoginInfo(){
        if(!self::isAuthenticated()){
            return null;
        }
        return json_decode($_COOKIE['login_information']);
    }
    public function setLoginInfo(){
        $logins = new Logins();
        $logins->login_id = $this->login_id;
        $logins->user_id = $this->Id;
        $res = $logins->add();
        if($res->status){
            setcookie('login_information', json_encode($this), time() + (3600*3), "/","",false); // 86400 = 1 day
        }
        else{
            die($res->msg);
        }
    }
    
    public static function destroyLoginInfo(){
        $loginInfo = self::getLoginInfo();
        if(!is_null($loginInfo)){   
            //updating the same at server also
            $login = new Logins();
            $login = $login->find($loginInfo->login_id);
            $login->logout_time = date('Y-m-d H:i:s');
            $login->save();
        }
        unset($_COOKIE['login_information']);
        setcookie('login_information', "", time(), "/","",false);
    }
    
    //this will update the login information stored in session
    public static function updateLoginInfo(UserViewModel $userViewModel){
        $loginInfo = self::getLoginInfo();
        if(!is_null($loginInfo)){            
            $loginInfo->Email = $userViewModel->Email;
            $loginInfo->PhoneNumber = $userViewModel->PhoneNumber;
            $loginInfo->UserName = $userViewModel->UserName;
            setcookie('login_information', json_encode($loginInfo), time() + (60*20), "/","",false);
        
            //updating the same at server also
            $login = new Logins();
            $login = $login->find($loginInfo->login_id);
            
            $Timestamp = strtotime(date('Y-m-d H:i:s'));//current timestamp
            $TotalTimeStamp = strtotime('+ 20 minutes', $Timestamp);//timestamp after 20 minutes
            $login->expiry = date('Y-m-d H:i:s',$TotalTimeStamp);
            $login->save();            
        }
    }
    
}
