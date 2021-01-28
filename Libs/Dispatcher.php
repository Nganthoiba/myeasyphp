<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;

/**
 * Description of Dispatcher
 * The main function of Dispatcher class is to get the right controller, 
 * action and the parameters with the help of router, when the router finds that 
 * HTTP request's URL matches any of the registered route patterns in the route table 
 * then the router forwards the request to the appropriate handler (which can be 
 * Controller and Action or just a function) for that request. 
 * 
 * Dispatcher also synchronizes the positions of the arguments w.r.t the parameters 
 * of the function or action of the controller, because dispatch will call that function 
 * or action of the controller by passing those arguments.
 * 
 * @author Nganthoiba
 * 
 *  $request        :   HTTP Request object
 *  $routeParams    :   Route parameters
 */
use MyEasyPHP\Libs\Request;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\MyEasyException;
use MyEasyPHP\Libs\Authorization;
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\EasyEntity;
use MyEasyPHP\Libs\Attributes\Http\Verbs\HttpMethod;

use ReflectionMethod;
use ReflectionFunction;

class Dispatcher {    
    public static $request;
    private static $routeParams;//route parameters
    public static function dispatch(){
        global $router; //Router Object
        //Getting user request information
        self::$request = new Request(); 
        //Getting uri requested by user
	$uri = 	!is_null(Request::getURI())?rtrim(Request::getURI(),'/'):Request::getURI();
        
        $router->setUri($uri);
        $router->extractComponents();//extract Controller and action wrt the request uri from the routes
        //senitising and filtering vulnerable and risky characters from all input values via GET or POST methods
        self::$routeParams = self::$request->cleanInputs($router->getParams());  
        $http_methods = $router->getMethods();
        
        if(!empty($http_methods) && !in_array(self::$request->getMethod(), $http_methods/*getting HTTP verbs*/)){                    
            $exc = new MyEasyException("Method not allowed.",405);
            $exc->httpCode = 405;
            $exc->setFile('');
            $exc->setLine(-1);
            $exc->setDetails("Methods allowed for the route '".$router->getRouteUrl()."' :- ".implode(', ',$http_methods).", but your request method is ".self::$request->getMethod());
            throw $exc;
        }
        //If route is mapped to only a function
        if($router->isOnlyFunction()){
            self::executeRouteFunction();
        }
        else{ 
            //If route is mapped to only a Controller and action
            self::executeControllerAction();
        }
    }
    //function to synchronize the positions of the arguments w.r.t the parameters of the function
    public static function synchroniseParameters(array $parameters=[],array $arguments=[]):array
    {   
        global $router;
        /*
         * $parameters are those defined in the action method of the controller
         * and the $arguments is the array of arguments that will be passed to that action method
         */
        $limit = sizeof($parameters);        
        for($i = 0; $i < $limit; $i++){  
            //break the iteration if the function or method is not accepting 
            //any further parameter
            if(!isset($parameters[$i])){break;}
            //finding out the data type of each parameter
            $type = ($parameters[$i]->getType())==null?'NULL':$parameters[$i]->getType()->getName();
            switch($type){
                case 'int': case 'float': case 'string': case 'NULL': 
                    $arguments = self::synchronizeOptionalArguments($arguments, $parameters, $i);
                    break;
                case 'bool': case 'resource':
                    break;
                case 'array':
                    /*
                 *
                 * If method of a controller accepts parameter of typed array, then it is going to 
                 * accept all the parameters in array structure. The implementations have also been 
                 * tested. For example: If we have a method public function test(array $args){}, and 
                 * if the route is configured as /test/{a}/{b} where those a and b enclosed by curly 
                 * braces are parameters, such parameters are structured in an associative array form 
                 * as ['a'=><<some_value1>>,'b'=>'<<some_value2>>'] and passed to the method or function. 
                 * And the variable $args has those parameters.
                 */
                    $arguments = self::insertItemInArray(self::$routeParams,$i,$arguments);
                    break;
                default:                        
                    //putting object as argument in its correct position with respect to parameters
                    //of the function or method
                    $object = new $type(); 
                    $arguments = self::insertItemInArray(self::setObjectData($object),$i,$arguments);

            }//end switch
        }//end foreach
        return $arguments;
    }
    
    //function to set data of a model object
    public static function setObjectData(object $object):object{
        if($object instanceof Model or $object instanceof EasyEntity){
            $object->setModelData(self::$request->getData());
        }
        return $object;
    }
    
    private static function insertItemInArray($item, int $position, array $arr = array ()):array
    {
        $slice1 = \array_slice($arr, 0, $position,true);
        array_push($slice1,$item);
        $slice2 = \array_slice($arr, $position, sizeof($arr)-$position,true);
        return array_merge($slice1,$slice2);
    }
    
    private static function synchronizeOptionalArguments(array $arguments, array $parameters, int $i/*position*/):array
    {
        if($parameters[$i]->isDefaultValueAvailable()){
            if(is_array($parameters[$i]->getDefaultValue()))
            {
                $arguments = self::insertItemInArray(self::$routeParams,$i,$arguments);
            }
            else if(!isset($arguments[$i]) || $arguments[$i]===":optional"){
                //:optional means the argument has been declared optional but its value of argument has not been set yet
                //so the argument is going to be set the default value of the parameter
                $arguments[$i] = $parameters[$i]->getDefaultValue();
            }
        }
        
        else{
            /*if(!isset($arguments[$i]) || $arguments[$i]===":optional"){
                $exc = new MyEasyException("Missing argument for the required parameter $".$parameters[$i]->getName()."...", 400);
                $exc->setDetails("Please check route configuration Config/routes.php file for the requested url "
                        . "and please also make sure you have supplied the sufficient arguments "
                        . "required for the function or method of the controller class.");
                throw ($exc);
            }*/
        }
        return $arguments;
    }
    
    //method to be called only when route url has mapped with a function
    private static function executeRouteFunction() {
        global $router;
                
        $function = $router->getFunction();
        $reflectionFunc = new ReflectionFunction($function);
        $syncParams = self::synchroniseParameters($reflectionFunc->getParameters(), array_values(self::$routeParams));

        $res = call_user_func_array($function, $syncParams);
        if(is_null($res)){
            exit();
        }
        echo $res;
    }
    //method to be called when route specifies 
    //what Controller Class will be instantiated and what method to invoke
    private static function executeControllerAction() {
        global $router,$controllerObj;
        $controller = is_null($router->getController())?"Controller":ucfirst($router->getController())."Controller";
        $action = is_null($router->getAction())?Config::get('default_action'):$router->getAction();//Action name
        self::initiateController($controller, $action);   
        
        $reflectionMethod = new ReflectionMethod($controllerObj, $action); 
        if(!self::isHttpMethodAllowed($reflectionMethod)){                    
            exit();
        }        
        $syncParams = self::synchroniseParameters($reflectionMethod->getParameters(),\array_values(self::$routeParams));

        $view = call_user_func_array([$controllerObj,$action], $syncParams);
        //Controller Action may returns view or json data depending upon whether the controller is api controller or just controller, 
        //and it is going to print whatever value returned.
        if(is_null($view)){
            exit();
        } 
        if($view instanceof View){                           
            //preventing clickjacking as the page can only be displayed in a frame 
            //on the same origin as the page itself.
            header('X-Frame-Options: SAMEORIGIN');                     
        }
        echo ($view); 
    }
    
    //function to instantiate a controller object
    private static function initiateController(string $controller,string $action){
        global $controllerObj;
        //Checking if resource for the requested URI exist or not
        if(!self::isResourceAvailable($controller, $action)){
            $exception = new MyEasyException("Sorry, the page you are looking for is not found on this server.");
            $exception->setDetails("Please check whether the requested url is registered in route configuration file Config/route.php.");
            $exception->httpCode = HttpStatus::HTTP_NOT_FOUND;
            $exception->setFile('');
            throw $exception;
        }        
        //*** creating Controller Object ***/
        $controller_class = CONTROLLER_NAMESPACE.$controller;        
        $controllerObj = new $controller_class();//instantiate a new controller object
        $controllerObj->setRequest(self::$request);
        $controllerObj->setParams(self::$routeParams);//setting parameters
        
        //If the controller is not an api controller then check if the user is authorized
        if($controllerObj instanceof Controller){
            startSecureSession();
            //check if method (action) to be invoked is authorised for the user
            if(!Authorization::isAuthorized($controllerObj,$action)){
                $msg = "Unauthorize access. You are not allowed to access the page. <a href='".Config::get('host')."/Accounts/login'>Login</a> with "
                        . "an authorized account. ";
                $exc = new MyEasyException($msg); 
                $exc->httpCode = HttpStatus::HTTP_FORBIDDEN;
                $exc->setFile('');
                $exc->setLine(-1);
                throw $exc;
            }
        } 
    }
    
    //function to get all the http methods set for the action method using attribute
    private static function getAllowedHttpMethods(ReflectionMethod $reflectionMethod){
        $httpMethods = [];
        foreach($reflectionMethod->getAttributes() as $attribute){
            $attributeInstance = $attribute->newInstance();
            if($attributeInstance instanceof  HttpMethod){
                $httpMethods = array_merge($httpMethods,$attributeInstance->getMethod());                
            }
        }
        return $httpMethods;
    }
    
    private static function isHttpMethodAllowed(ReflectionMethod $reflectionMethod){
        global $router;
        $httpMethods = self::getAllowedHttpMethods($reflectionMethod);
        if(empty($httpMethods) || in_array(self::$request->getMethod(), $httpMethods)){
            return true;
        }
        $exc = new MyEasyException("Http method is not allowed.");
        $exc->httpCode = HttpStatus::HTTP_FORBIDDEN;
        $exc->setFile('');
        $exc->setDetails("Methods allowed for the route '".$router->getRouteUrl()."' :- ".implode(', ',$httpMethods).", but your request method is ".self::$request->getMethod());
        throw $exc;
    }
    
    //This method checks if controller class or method exists or not
    private static function isResourceAvailable(string $controller,string $action):bool{
        $controller = CONTROLLER_NAMESPACE.$controller;
        if(!class_exists($controller, TRUE)){
            return false;
        }
        if(!method_exists($controller, $action)){
            return false;
        }
        return true;
    }
}
