<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;

/**
 * Description of Dispatcher
 * The main function of Dispatcher class is to grab the url from the client request, 
 * then pass the request uri to the right controller name and action name from the list 
 * of routes, then execute the action of the controller.
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Request;
use MyEasyPHP\Libs\Config;
use Exception;
use MyEasyPHP\Libs\MyEasyException;
use MyEasyPHP\Libs\Authorization;
use MyEasyPHP\Libs\ApiController;
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\EasyEntity;

use ReflectionMethod;
use ReflectionFunction;

class Dispatcher {    
    public static $request;
    public static function dispatch(){
        global $router; //Router Object
        global $controllerObj;//Controller Object
        //Getting user request information
        self::$request = new Request(); 
        //Getting uri requested by user
        $uri = Request::getURI();
		
        if(!is_null($uri)){
            $uri = (rtrim($uri,'/'));
        }
        
        $router->setUri($uri);
        $router->extractComponents();//extract Controller and action wrt the request uri from the routes
        
        $methods = $router->getMethods();//getting HTTP verbs       
        //senitising all input values via GET or POST methods
        $params = self::$request->senitizeInputs($router->getParams());       
        
        //If router is only a function
        if($router->isOnlyFunction()){
            if(!in_array(self::$request->getMethod(), $methods)){                    
                $exc = new MyEasyException("Method not allowed.",405);
                $exc->setDetails("Methods allowed for the route '".$router->getRouteUrl()."' :- ".implode(', ',$methods)." but your request method is ".self::$request->getMethod());
                throw $exc;
            }
            $function = $router->getFunction();
            $reflectionFunc = new ReflectionFunction($function);
            $params = self::synchroniseParameters($reflectionFunc->getParameters(), $params);
            
            if(sizeof($params)>0){
                //executing the function
                $res = call_user_func_array($function, $params);
            }else{
                $res = $function();//executing the function
            }
            if(is_null($res)){
                http_response_code(102);
                exit();
            }
            echo $res;
        }
        else{            
            $controller = is_null($router->getController())?"Controller":ucfirst($router->getController())."Controller";
            $action = $router->getAction();//Action name
            if(is_null($action)){
                $action = Config::get('default_action');
            }
			
            //*** creating Controller Object ***
            $controller_class = CONTROLLER_NAMESPACE.$controller;
            if(!class_exists($controller_class, TRUE)){
                $exception = new MyEasyException("Sorry, the page you are looking for is not found.",404);
                $exception->setDetails("Controller file ".$controller." class does not exist. Please check.");
                throw $exception;
            }
            $controllerObj = new $controller_class();//instantiate a new controller object
            
            $controllerObj->setRequest(self::$request);//very much necessary
            $controllerObj->setParams($params);//setting parameters is very much necessary
            $reflection = new ReflectionMethod($controllerObj, $action);
            //Here check whether the controller is an object of ApiController or just normal Controller
            if(($controllerObj instanceof ApiController)){
                //checking whether the request method is allowed for accessing URI(For security)
                
                if(!in_array(self::$request->getMethod(), $methods)){                    
                    $resp = $controllerObj->response->set([
                        "status"=>false,
                        "status_code"=>405,
                        "error"=>"Methods allowed for the route '".$router->getRouteUrl()."' are: ".implode(', ',$methods).", but your request method is ".self::$request->getMethod()
                    ]);
                    echo $controllerObj->sendResponse($resp);
                    exit();
                }
                
                //check if method (action) exists for the controller class
                if(!method_exists($controllerObj, $action)){
                    $resp = $controllerObj->response->set([
                        "status"=>false,
                        "status_code"=>404,
                        "msg"=>"Resource not found!"
                    ]);
                    echo $controllerObj->sendResponse($resp);
                    exit();
                }
                
                //ensuring only public method or action to be allowed to access
                if (!$reflection->isPublic()) {
                    $resp = $controllerObj->response->set([
                        "status"=>false,
                        "status_code"=>403,
                        "msg"=>"Access is denied."
                    ]);
                    echo $controllerObj->sendResponse($resp);
                    exit();
                }                
            }
            else{                
                //checking whether the request method is allowed for accessing URI(For security)
                if(!in_array(self::$request->getMethod(), $methods)){
                    $exc = new MyEasyException("Method not allowed.",405);
                    $exc->setDetails("Methods allowed for the route '".$router->getRouteUrl()."' :- ".implode(', ',$methods)." but your request method is ".self::$request->getMethod());
                    throw $exc;            
                }
                //check if method (action) exists for the controller class
                if(!method_exists($controllerObj, $action)){
                    $exc = new MyEasyException("The page you are looking for does not exist.",404);
                    $exc->setDetails(" Action '".$action."' of controller class '"
                            .$controller."' does not exist.");
                    throw $exc;
                }
                //check if method (action) to be invoked is authorised for the user
                if(!Authorization::isAuthorized($controllerObj,$action)){
                    $msg = "Unauthorize access. You are not allowed to access the page. <a href='".Config::get('host')."/Accounts/login'>Login</a> with "
                            . "an authorized account. ";
                    $exc = new MyEasyException($msg,403);              
                    throw $exc;
                }
                
                //ensuring only public method or action to be allowed to access otherwise
                //access will be denied.
                if (!$reflection->isPublic()) {
                    throw new MyEasyException("Access denied.",403);
                }                
                //If the controller is not an api controller
                startSecureSession();
            }  
            /*echo "<pre>";
            print_r($params);
            echo "</pre>";*/
            $params = self::synchroniseParameters($reflection->getParameters(),$params);
            
            try{
                ///checking whether parameter exists or not
                if(sizeof($params)>0){
                    $view = call_user_func_array([$controllerObj,$action], $params);
                }
                else{
                    $view = $controllerObj->$action();
                }
                //Controller Action may returns view or json data depending upon whether the controller is api controller or just controller, and it is going to be printed
                if(is_null($view)){
                    http_response_code(102);
                    exit();
                    //echo "Null";
                } 
                else if($view instanceof View){                           
                    //if it is view object then render its contents
                    header('X-Frame-Options: SAMEORIGIN');//preventing clickjacking as the page can only be displayed in a frame on the same origin as the page itself. 
                    //header('X-Frame-Options: deny');
                    //The page cannot be displayed in a frame, regardless of the site 
                    //attempting to do so.
                }
                echo ($view);  
            }
            catch(TypeError $error){
                if($controllerObj instanceof ApiController){
                    $resp = $controllerObj->response->set([
                        "status"=>false,
                        "status_code"=>500,
                        "msg"=>"Whoops! An error has occured.",
                        "error"=>$error->getMessage()
                    ]);
                    echo $controllerObj->sendResponse($resp);
                    exit();
                }
                throw $error;
            }
            catch(Exception $e){
                if($controllerObj instanceof ApiController){
                    $resp = $controllerObj->response->set([
                        "status"=>false,
                        "status_code"=>500,
                        "msg"=>"Whoops! An error has occured."
                    ]);
                    echo $controllerObj->sendResponse($resp);
                    exit();
                }
                throw $e;
            }
        }
    }
    //function to synchronise parameters and arguments
    public static function synchroniseParameters(array $parameters,array $arguments):array
    {   
        global $router;
        /*
         * $parameters is the array of parameters acceptable by the action method of the controller
         * and the $arguments is the array of arguments that will be passed to that action method
         */
        //php data types
        $php_datatypes = ['int','float','bool','object','array','NULL','string','resource'];
        $arguments = array_values($arguments);
        //if no of arguments is more than the no of parameters to be accepted by the 
        //action method, then synchronisation must be done according to arguments
        if(sizeof($arguments)>=sizeof($parameters)){
            for($i = 0; $i<sizeof($arguments); $i++){            
                if(!isset($parameters[$i])){
                    //break the loop if the function or method is not accepting 
                    //any further parameter
                    break;
                }
                //finding out the data type of each parameter
                $type = ($parameters[$i]->getType())==null?'NULL':$parameters[$i]->getType()->getName();
                if($type == 'array'){
                    $arguments[$i] = $router->getParams();
                }
                else if($type=='object' || (!in_array($type, $php_datatypes) && class_exists($type,TRUE))){
                    //It should be a Model object
                    $object = new $type();
                    $arguments = self::insertItemInArray($arguments,self::setObjectData($object),$i);
                }//end if
            }//end foreach
        }
        else{
            
            //if the no of parameters to be accepted by action method is more than the number of
            //arguments, then synchronisation must be done according to those parameters.
            for($i=0;$i<sizeof($parameters);$i++){
                //finding out the data type of each parameter
                $type = ($parameters[$i]->getType())==null?'NULL':$parameters[$i]->getType()->getName();
                if($type == 'array'){
                    $arguments[$i] = $router->getParams();
                }
                else if($type=='object' || (!in_array($type, $php_datatypes) && class_exists($type,TRUE))){                    
                    //It should be a Model object
                    $object = new $type(); 
                    if(!isset($arguments[$i])){
                        $arguments[$i] = self::setObjectData($object);
                    }
                    else{
                        //putting object as argument in its correct position with respect to parameters
                        //of the function or method
                        $arguments = self::insertItemInArray($arguments,self::setObjectData($object),$i);
                    } 
                }//end if
            }//end for
        }//end else
        return self::setOptionalParamValues($parameters,array_values($arguments));
    }
    
    //function to set data of a model object
    public static function setObjectData(object $object):object{
        if($object instanceof Model or $object instanceof EasyEntity){
            $object->setModelData(self::$request->getData());
        }
        else{
            foreach(self::$request->getData() as $key=>$val){
                $object->{$key} = $val; 
            }
        }
        return $object;
    }
    //setting default values for optional parameters of a method
    private static function setOptionalParamValues(array $parameters, array $arguments):array
    {
        for($i=0;$i<sizeof($parameters);$i++){
            //if optional parameter is found as per route url, then suitable value must
            //be set
            if(is_object($arguments[$i])){
                continue;
            }
            if($arguments[$i] == ":optional"){
                if($parameters[$i]->isOptional()){
                    $arguments[$i] = $parameters[$i]->getDefaultValue();
                }
                else
                {
                    //Arguments which have beed declared as optinal must have its default
                    //value in the function parameter otherwise those arguments have to be 
                    //removed.
                    unset($arguments[$i]);
                }
            }
        }
        return $arguments;
    }
    
    private static function insertItemInArray(array $arr=[],$item,int $position):array
    {
        $j = sizeof($arr);
        while($j > $position){
            $arr[$j] = $arr[$j-1];
            $j--;
        }
        $arr[$j] = $item;
        return $arr;        
    }    
}
