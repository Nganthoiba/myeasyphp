<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models\Entities;

/**
 * Description of PasswordResetLinks
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\EasyEntity as Entity;
use MyEasyPHP\Libs\Attributes\Key;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="PasswordResetLinks")
 */
class PasswordResetLinks extends Entity{
    /** @ORM\Id @ORM\Column(type="string",length=255) */
    #[Key]
    public $resetcode;
    
    /** @ORM\Column(type="datetime", length=15) */
    public $createdat;
    
    /** @ORM\Column(type="datetime", nullable=true) */
    public $deletedat;
    
    /** @ORM\Column(type="datetime", nullable=true) */
    public $expiry;
    
    /** @ORM\Column(type="string", length=15) */
    public $source_ip;
    
    /** @ORM\Column(type="string", length=255) */
    public $device;//user_agent, 
    
    /** @ORM\Column(type="string", length=36) */
    public $userid;
    
    public function generate(string $UserId){
        $this->userid = $UserId;
        $this->createdat = date("Y-m-d H:i:s");
        $this->resetcode = randId(150);
        $this->expiry = $this->getExpiryTime();
        $this->device = filter_input(INPUT_SERVER,'HTTP_USER_AGENT');
        $this->source_ip = get_client_ip();
    }
    
    private function getExpiryTime(){
        $Timestamp = strtotime(date('Y-m-d H:i:s'));//current timestamp
        $TotalTimeStamp = strtotime('+ 10 minutes', $Timestamp);//timestamp after 20 minutes
        return date('Y-m-d H:i:s',$TotalTimeStamp);
    }
}
