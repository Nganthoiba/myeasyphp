<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;
/*
 * How do MVC routers work
 * A MVC Router class inspects the URL of an HTTP request and attempts to match individual URL 
 * components to a Controller and a method defined in that controller, passing along any arguments
 * to the method 
 * defined.
 * Reference: https://www.codediesel.com/php/how-do-mvc-routers-work/
 * 
 * The purpose of this class is to maintain set of Routes, each route maps the URL of an HTTP 
 * request with a function or with controller and action. It also means a url can determine, 
 * what function to execute and which controller and action to execute. A router has a collection 
 * of such route objects. 
 * 
 * Router also checks whether the url of an HTTP request is available in 
 * the set of routes or not, if found router will get either the function or Controller name 
 * and method name/Action name, and the parameters which will be passed as arguments to the function or method.
 * Otherwise if not found it breaks down thr url requested by user into three segments:-
 * Controller, Action, and parameters. 
 * 
 * Router is used by Dispatcher module to get the controller name and method name of a URL.
 * #Note: Routes object is a collection of  objects of class 'Route'
 
 * Examples: * 
 * 
 * $router = new Router();
 * $router->addRoute(param1,param2,param3[optional]);
 * The first parameter is the request uri or url, and the second parameter can be just a function
 * or an associative array indicating which controller and what action to be called.
 * and lastly the third parameter is the methods(http verbs) which is allowed for the request url, its values 
 * should be passed in the form of array like ['POST','PUT'] or in the string format separated by | character
 * like "POST|PUT", this third parameter is optional, if you don't pass, by default its value is GET, which means 
 * the route is accessible by GET method. Below is a list of exxamples:
 * 
 * @author Nganthoiba
 * 19/06/2020
 */
use MyEasyPHP\Libs\Route;
use MyEasyPHP\Libs\Config;
use Exception;
class Router {
    protected $uri; //request uri
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
    
    //Grouping name of Routes;
    protected $group_name;


    public function __construct($uri="") {
        //Setting default values
        $this->routes = [];
        $this->controller = Config::get('default_controller');
        $this->action = Config::get('default_action');
        $this->params = [];
        $this->methods = ['GET']; //by default
        $this->is_only_function = false;
        $this->function_name = "";
        $this->group_name = "";
    }
    
    /* The methods adds each route defined to the $routes array */
    //callable: means $parameters can be just a function
    public function addRoute($url, /*callable*/ $parameters, $methods=[]/*HTTP Verbs in array*/){
        if(!is_null($this->group_name) && $this->group_name!== ""){
            $url = '/'.trim($this->group_name, '/').'/'.trim($url,'/');
        }
        $url = (trim($url)=='/' || trim($url)=="")?'/':rtrim($url,'/');
        $this->routes[] = new Route($url, $parameters, $methods);
        return $this;
    }
    
    //function for Grouping Routes
    public function group(string $group_name="", callable $func /*function*/){ 
        global $router;
        $old_group_name = $this->group_name;
        if(!is_null($group_name) && $group_name!== ""){
            $this->group_name = trim($this->group_name,'/').'/'.trim($group_name,'/');
            call_user_func($func,$router);
        }
        //resetting group name to the earlier one, because group functions can be nested
        //that is one group inside another group.
        $this->group_name = $old_group_name;
        unset($old_group_name);
    }
    
    /* method to get all routes */
    public function getRoutes():array{
        return $this->routes;
    }
    /* method to get uri */
    public function getUri():string{
        return $this->uri;
    }
    /* method to set uri */
    public function setUri($uri):void{
        $this->uri = (is_null($uri) || trim($uri)=="")?(filter_input(INPUT_GET, "uri", FILTER_SANITIZE_SPECIAL_CHARS)):trim($uri);
        //$this->uri = rtrim($this->uri,'/');        
    }
    public function getController():string{
        return $this->controller;
    }
    public function getAction():string{
        return $this->action;
    }
    public function getMethods():array{
        return $this->methods;
    }
    public function getRouteUrl():string{
        return $this->routeUrl;
    }
    
    public function getParams():array{
        return $this->params;
    }
    public function getFunction(): callable{
        return $this->function_name;
    }
    
    //This method will break down a uri into three components: Controller, Action and Parameters
    public function extractComponents() : void{
        
        /* Check if the given route path for the URI is already defined, then fetch the route object to find out 
         * controller name and action name
         **/
        $path = "/".$this->uri;
        $route = $this->findRoute($path);
        //if route is found...
        if(!is_null($route)){
            $this->routeUrl = $route->getPath();
            //if route is an executable function
            if($route->isFunction()){
                $this->is_only_function = true;
                $this->function_name = $route->getFunction();
                $this->methods = $route->getMethods();
            }
            else{
                $this->controller = $route->getController();
                $this->action = $route->getAction();
                $this->methods = $route->getMethods();   
            }
        }        
        else{
            //If there is no route set for the particular URI specifically, then by default break down the request url into 3 main segments
            //which means the first is the Controller, the second one is the action and the remainings are the parameters
            
            $uri = urldecode(trim("/".$this->uri,'/'));
            $this->routeUrl = $uri;
            $uri_parts = explode('?',$uri);
            $paths =$uri_parts[0];
            $path_parts = explode('/', $paths);

            if(isset($path_parts)){
                if(current($path_parts)){
                    //First part is considered as controller name
                    $this->controller = trim(htmlspecialchars(strip_tags(current($path_parts))));
                    array_shift($path_parts);
                }
                if(current($path_parts)){
                    //Second part is considered as the action name
                    $this->action = trim(htmlspecialchars(strip_tags(current($path_parts))));                    
                    array_shift($path_parts);
                }
                $this->params = $path_parts;
            }
        }
    }// end of extractComponents
    
    //method return whether route has an executable function
    public function isOnlyFunction():bool{
        return $this->is_only_function;
    }
    
    //compare two urls, to decide whether they are equal or not
    private function areEqualURLs($first_url/*url set in route config*/, $second_url/*user requested url*/): bool{
        //$this->params = [];//reseting parameters
        
        $first_url_parts = explode('/',$first_url);
        $second_url_parts = explode('/', $second_url);
        if(sizeof($first_url_parts) < sizeof($second_url_parts)){
            return false;
        }
        //Further go for checking for every corresponding element
        $limit = sizeof($first_url_parts);
        for($i = 0; $i < $limit; $i++){
            $part=isset($second_url_parts[$i])?strtolower($second_url_parts[$i]):null;
            if(strtolower($first_url_parts[$i]) !== $part){
                if(!$this->isParameter($first_url_parts[$i])){
                    return false;
                }
                //removing curly braces
                $index = ltrim($first_url_parts[$i],'{');
                $index = rtrim($index,'}');
                if(isset($second_url_parts[$i])){                    
                    $this->params[str_replace(":optional", "", $index)] = $second_url_parts[$i];  
                }
                else{ 
                    if(strpos($index, ":optional")!==false){
                    //This means that if user hits the url without an optional parameter, then 
                    //the parameter will store string ":optional" as value, this value will be again
                    //checked at the Dispatcher class and set the default value according
                    //to the called function or method.
                        $this->params[str_replace(":optional", "", $index)] = ":optional";
                    }
                }
            }
        }//end of parsing every single corresponding element
        unset($limit);
        return true;
    }
    
    //method to check whether a url segment indicates a parameter or not, anything(segment) in the route url enclosed 
    //by curly braces is to be recognised as a parameter
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