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
class Persons extends Entity{
    
    public  $person_name,
            $id ,
            $phone_no,
            $address;
    public function __construct() {
        parent::__construct();
        $this->setTable("persons")->setKey("id");
    }
}
