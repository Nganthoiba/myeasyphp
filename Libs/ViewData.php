<?php
namespace MyEasyPHP\Libs;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ViewData
 *
 * @author Nganthoiba
 */
class ViewData {
    public $content;
    public $detail ;
    public $ErrorMessage ;
    public function __construct($data = array()){
        $this->detail="";
        $this->ErrorMessage="";
        $this->set($data);
    }
    //method to set view data
    public function set($data=array()){
        foreach($data as $k=>$v){
            $this->{$k} = $v;
        }
    }
    //method to get view data
    public function get($key){
        if(trim($key) === ""){
            return null;
        }
        return (isset($this->{$key}))?$this->{$key}:null;
    }
}
