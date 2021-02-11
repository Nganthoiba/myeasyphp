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
use MyEasyPHP\Libs\Attributes\Key;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="Logins")
 */
class Logins extends Entity{
    /** @ORM\Id @ORM\Column(type="string",length=255) */
    #[Key]
    public  $login_id;/*primary key*/
    
    /** @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"}) */
    public  $login_time;
    
    /** @ORM\Column(type="datetime", nullable=true) */
    public  $logout_time;   
    
    /** @ORM\Column(type="datetime", nullable=true) */
    public  $expiry;
    
    /** @ORM\Column(type="string", length=15) */
    public  $source_ip;
    
    /** @ORM\Column(type="string", length=255) */
    public  $device;//user_agent, 
    
    /** @ORM\Column(type="string", length=36) */
    public  $user_id;
    
    
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
