<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models\Entities;

/**
 * Description of Roles
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\EasyEntity as Entity;
class Roles extends Entity{
    public $role_id;
    public $role_name;
    public function __construct() {
        parent::__construct();
        $this->setKey("role_id");
    }
}
