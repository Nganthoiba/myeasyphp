<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models\Entities;

/**
 * Description of Persons
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\EasyEntity as Entity;
use Doctrine\ORM\Mapping as ORM;
use MyEasyPHP\Libs\Attributes\Key;
/**
 * @ORM\Entity
 * @ORM\Table(name="Persons")
 */
class Persons extends Entity{
    
    /**
     * @ORM\Id 
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue **/
    #[Key]
    public  $id ;
    /** 
     * @ORM\Column(type="string",length=10) 
     * **/
    public  $phone_no;
    /** 
     * @ORM\Column(type="string",length=255) 
     * **/
    public  $address;
    /** 
     * @ORM\Column(type="string",length=65) 
     * **/
    public  $person_name;
}
