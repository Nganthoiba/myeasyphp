<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models\Entities;

/**
 * Description of Logins
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\EasyEntity as Entity;
use MyEasyPHP\Libs\Response;
class Logins extends Entity{
    public  $login_id,/*primary key*/
            $login_time,
            $logout_time,
            $expiry,   
            $source_ip,
            $device,//user_agent, 
            $user_id;
    public function __construct() {
        parent::__construct();
        $this->setKey("login_id")->setTable("logins");
    }
    //Adding user login details
    public function add():Response{
        //$this->login_id = randId(60);
        $this->source_ip = get_client_ip();
        $this->device = filter_input(INPUT_SERVER,'HTTP_USER_AGENT');
        $this->login_time = date('Y-m-d H:i:s');
        $Timestamp = strtotime($this->login_time);
        $TotalTimeStamp = strtotime('+ 3 hours', $Timestamp);//timestamp after 20 minutes
        $this->expiry = date('Y-m-d H:i:s',$TotalTimeStamp);//expiry date time set just at 20 minutes after login
        
        return parent::add();
    }
}
