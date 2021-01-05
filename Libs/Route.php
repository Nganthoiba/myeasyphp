<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;

/**
 * Description of Route
 * Route is a class which maps the HTTP request url with the controller-action or with a
 * function. It determines what function or controller-action to be called or invoked, when 
 * a user requests a url. A list of such Route objects are stored in the Router class.
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
        //mapping is done at constructor        
        $this->path = ($path);
        $this->isFunction = is_callable($params);
        if($this->isFunction){
            //$param is an executable function
            $this->function = $params;
        }
        else if(is_array($params)){
            $this->controller = $params['Controller']??"";
            $this->action = $params['Action']??"";            
        }
        if(is_string($methods) && trim($methods) !== ""){
            $exploded_methods = explode("|", $methods);
            $this->methods = sizeof($exploded_methods)>0?$exploded_methods:$methods;
            unset($exploded_methods);
        }
        else if(is_array($methods)){
            $this->methods = sizeof($methods)==0?['GET']:$methods;//by default set to GET method
        }
        //convert every methods in uppercase
        for($i = 0; $i < sizeof($this->methods); $i++){            
            if(trim($this->methods[$i]) === ""){
                //discarding blank spaces
                continue;
            }
            $this->methods[$i] = strtoupper(trim($this->methods[$i]));
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
    
    //method to detect whether the route is for API
    public function isAPI(){
        $paths = explode("/", $this->path);
        if(isset($paths[0]) && strtolower($paths[0])=="api"){
            return true;
        }
        return false;
    }
}
