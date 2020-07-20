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
class Logins extends Entity{
    public  $login_id,/*primary key*/
            $login_time,
            $logout_time,
            $expiry,   
            $source_ip,
            $user_agent, 
            $user_id;
    public function __construct() {
        parent::__construct();
        $this->setKey("login_id")->setTable("Logins");
    }
}
