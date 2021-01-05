<?php
namespace MyEasyPHP\Libs;
/*
 * ViewData is used to pass data (which are not a part of model) from controller to view 
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
    public $ErrorDetail ;
    public function __construct($data = array()){
        $this->detail="";
        $this->ErrorMessage="";
        $this->ErrorDetail="";
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
    //to convert to array
    public function toArray():array{
        return json_decode(json_encode($this),true);
    }
}
