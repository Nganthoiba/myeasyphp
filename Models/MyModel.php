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
#[MyAttribute(value: 1234)]
class MyModel extends \MyEasyPHP\Libs\Model{
    public int $x;
    public int $y;
    public float $z;
    public string $name;
    public function sum(){
        return ($this->x+$this->y);
    }    
}
