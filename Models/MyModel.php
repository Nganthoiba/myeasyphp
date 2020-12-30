<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Models;

/**
 * Description of MyModel
 *
 * @author Nganthoiba
 */
class MyModel {
    public $x, $y;
    public function sum(){
        return ($this->x+$this->y);
    }
}
