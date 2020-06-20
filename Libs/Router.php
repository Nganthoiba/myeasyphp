<?php
declare(strict_types=1);
/*
 * How do MVC routers work
 * A MVC Router class inspects the URL of an HTTP request and attempts to match individual URL 
 * components to a Controller and a method defined in that controller, passing along any arguments to the method 
 * defined.
 * Reference: https://www.codediesel.com/php/how-do-mvc-routers-work/
 * 
 * The purpose of this class is to maintain set of URIs. It checks whether the url requested by user is available 
 * in the routes set or not, if found it breaks down thr url requested by user into three segments:-
 *  Controller, Action, and parameters.
 * #Note: Routes object is a collection of  objects of class 'Route'
 */

namespace MyEasyPHP\Libs;

/**
 * Description of Router
 *
 * @author Nganthoiba
 * 19/06/2020
 */
use MyEasyPHP\Libs\Route;
use Exception;
class Router {
    protected $uri;
    protected $routes = []; //set or collection of URIs(routes) defined by user
    
    ////////////ROUTE COMPONENTS///////////
    protected $controller;  // Name of the controller
    protected $action;      //action method
    protected $params;      //Parameters passed in url
    protected $method;      //HTTP Verbs GET, POST, PUT, DELETE etc which are allowed for accessing the cuttent url
    ///////////////////////////////////////    
    
    ////////////////////////IF ROUTE CONTAINS EXECUTABLE FUNCTION INSTEAD OF CONTROLLER AND ACTION///////////
    protected $is_only_function; //possible value is either true or false and by default its value is false
    protected $function_name; //by default its value is blank
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public $test_val;
    
    public function __construct($uri="") {
        //Setting default values
        $this->routes = [];
        $this->params = [];
        $this->methods = ['GET']; //by default
        $this->is_only_function = false;
        $this->function_name = "";
    }
    
    /* The methods adds each route defined to the $routes array */
    //callable: means $parameters can be just a function
    public function addRoute($url, /*callable*/ $parameters, $methods=[]/*HTTP Verbs in array*/) {
        $this->routes[] = new Route($url, $parameters, $methods);
    }
    /* method to get all routes */
    public function getRoutes(){
        return $this->routes;
    }
    /* method to get uri */
    public function getUri(){
        return $this->uri;
    }
    /* method to set uri */
    public function setUri($uri){
        $this->uri = (is_null($uri) || trim($uri)=="")?(filter_input(INPUT_GET, "uri", FILTER_SANITIZE_SPECIAL_CHARS)):trim($uri);
        //$this->uri = rtrim($this->uri,'/');        
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
    public function getParams(){
        return $this->params;
    }
    public function getFunction(){
        return $this->function_name;
    }
    
    //This method will break down a uri into three components: Controller, Action and Parameters
    public function extractComponents() : void{
        //By default if there is no route set for the particular URI specifically
        $uri = urldecode(trim("/".$this->uri,'/'));
        $uri_parts = explode('?',$uri);
        $paths =$uri_parts[0];
        $path_parts = explode('/', $paths);

        if(count($path_parts)){
            if(current($path_parts)){
                //First part is considered as controller name
                $this->controller = current($path_parts); 
                array_shift($path_parts);
            }
            if(current($path_parts)){
                //Second part is considered as the action name
                $this->action = current($path_parts); 
                array_shift($path_parts);
            }
            $this->params = $path_parts;
        }
        $temp_params = $this->params;
        /* Check if the given route path for the URI is already defined, then fetch the route object to find out 
         * controller name and action name
         **/
        $path = "/".$this->uri;
        $route = $this->findRoute($path);
        if($route!=null){
            if($route->isFunction()){
                $this->is_only_function = true;
                $this->function_name = $route->getFunction();
            }
            else{
                $this->controller = $route->getController();
                $this->action = $route->getAction();
                $this->methods = $route->getMethods();   
            }
        }
        else{
            $this->params = $temp_params;
            unset($temp_params);
        }
        
    }// end of extractComponents
    
    //method return whether route has an executable function
    public function isOnlyFunction(){
        return $this->is_only_function;
    }
    
    //compare two urls, to decide whether they are equal or not
    private function areEqualURLs($first_url, $second_url){
        $this->params = [];//reseting parameters
        
        $first_url_parts = explode('/',$first_url);
        $second_url_parts = explode("/", $second_url);
        
        if(sizeof($first_url_parts) !== sizeof($second_url_parts)){
            return false;
        }
        
        //Further go for checking for every corresponding element
        for($i = 0; $i < sizeof($first_url_parts); $i++){
            if($first_url_parts[$i] !== $second_url_parts[$i]){
                if(!$this->isParameter($first_url_parts[$i])){
                    return false;
                }
                //removing curly braces
                $index = trim($first_url_parts[$i],"{");
                $index = trim($index,"}");
                $this->params[$index] = $second_url_parts[$i];                
            }
        }//end of parsing every single corresponding element
        return true;
    }
    
    //method to check whether a url segment indicates a parameter or not
    private function isParameter($url_segment){
        return preg_match('/{(.*?)}/', $url_segment);
    }
    
    //method to find out whether a route is defined for the given url
    private function findRoute($url){
        if(is_null($url)){
            return null;
        }
        $routes = $this->routes;
        foreach($routes as $route){
            if($url == $route->getPath() || $this->areEqualURLs($route->getPath(), $url)){
                return $route;
            }
        }
        return null;
    }
}
