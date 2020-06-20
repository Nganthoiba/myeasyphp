<?php
declare(strict_types=1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Libs;

/**
 * Description of Route
 *
 * @author Nganthoiba
 */
class Route {
    private $path;//the request url
    private $controller;//what controller will be executed
    private $action;//what action to be invoked for the request url
    private $methods;//the types of method allowed for the request url
    private $isFunction;//whether the route has executable function
    private $function;
    
    
    public function __construct(string $path, $params, $methods) {
        $this->path = $path;
        $this->isFunction = is_callable($params);
        if($this->isFunction){
            //$param is an executable function
            $this->function = $params;
        }
        else if(is_array($params)){
            $this->controller = $params['Controller']??"";
            $this->action = $params['Action']??"";            
        }
        if(is_string($methods)){
            $this->methods = explode("|", $methods);
        }
        else if(is_array($methods)){
            $this->methods = sizeof($methods)==0?['GET']:$methods;//by default set to GET method
        }
        //convert every methods in uppercase
        for($i = 0; $i < sizeof($this->methods); $i++){
            $this->methods[$i] = strtoupper($this->methods[$i]);
        }
    }
    
    public function getPath(){
        return $this->path;
    }
    public function getController(){
        return $this->controller;
    }
    public function getAction(){
        return $this->action;
    }
    public function getMethods(){
        return $this->methods;
    }
    
    public function isFunction(){
        return $this->isFunction;
    }
    
    public function getFunction(){
        return $this->function;
    }
}
