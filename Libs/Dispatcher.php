<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;

/**
 * Description of Dispatcher
 * The main function of Dispatcher class is to grab the url from the client request, 
 * then find out the suitable controller name and action name from the list 
 * of routes with he help of router, then execute the action of the controller.
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
            $params = self::synchroniseParameters($reflectionFunc->getParameters(), array_values($params));
            
            $res = call_user_func_array($function, $params);
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
            try{   
                //checking whether the request method is allowed for accessing URI(For security)
                if(!in_array(self::$request->getMethod(), $methods)){
                    $exc = new MyEasyException("Method not allowed.",405);
                    $exc->setDetails("Methods allowed for the route '".$router->getRouteUrl()."' :- ".implode(', ',$methods)." but your request method is ".self::$request->getMethod());
                    throw $exc;            
                }                              
                //If the controller is not an api controller
                if($controllerObj instanceof Controller){
                    startSecureSession();
                    //check if method (action) to be invoked is authorised for the user
                    if(!Authorization::isAuthorized($controllerObj,$action)){
                        $msg = "Unauthorize access. You are not allowed to access the page. <a href='".Config::get('host')."/Accounts/login'>Login</a> with "
                                . "an authorized account. ";
                        $exc = new MyEasyException($msg,403);              
                        throw $exc;
                    }
                }
                $reflection = new ReflectionMethod($controllerObj, $action);
                $params = self::synchroniseParameters($reflection->getParameters(),array_values($params));
            
                ///checking whether parameter exists or not
                $view = call_user_func_array([$controllerObj,$action], $params);
                //Controller Action may returns view or json data depending upon whether the controller is api controller or just controller, 
                //and it is going to print whatever value returned.
                if(is_null($view)){
                    http_response_code(102);
                    exit();
                    //echo "Null";
                } 
                if($view instanceof View){                           
                    //preventing clickjacking as the page can only be displayed in a frame 
                    //on the same origin as the page itself.
                    header('X-Frame-Options: SAMEORIGIN');                     
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
                        "msg"=>"Whoops! An error has occured.",
                        "error"=>$error->getMessage()
                    ]);
                    echo $controllerObj->sendResponse($resp);
                    exit();
                }
                throw $e;
            }
        }
    }
    //function to synchronise parameters and arguments
    public static function synchroniseParameters(array $parameters=[],array $arguments=[]):array
    {   
        global $router;
        /*
         * $parameters is the array of parameters acceptable by the action method of the controller
         * and the $arguments is the array of arguments that will be passed to that action method
         */
        //if number of arguments is more than the number of parameters to be accepted by the 
        //action method, then synchronisation must be done according to arguments
        $limit = sizeof($arguments)>sizeof($parameters)?sizeof($arguments):sizeof($parameters);
        
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
                    $arguments = self::insertItemInArray($arguments,$router->getParams(),$i);
                    break;
                default:                        
                    //putting object as argument in its correct position with respect to parameters
                    //of the function or method
                    $object = new $type(); 
                    $arguments = self::insertItemInArray($arguments,self::setObjectData($object),$i);

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
    
    private static function insertItemInArray(array $arr=[],$item,int $position):array
    {
        $slice1 = array_slice($arr, 0, $position,true);
        array_push($slice1,$item);
        $slice2 = array_slice($arr, $position, sizeof($arr)-$position,true);
        return array_merge($slice1,$slice2);
    }
    
    private static function synchronizeOptionalArguments(array $arguments, array $parameters, int $i/*position*/):array
    {
        if(!isset($arguments[$i]) || $arguments[$i]==":optional"){
            if($parameters[$i]->isOptional()){
                if(is_array($parameters[$i]->getDefaultValue())){
                    $arguments = self::insertItemInArray($arguments,$router->getParams(),$i);
                }
                else{
                    $arguments[$i] = $parameters[$i]->getDefaultValue();
                }
            }
            else{
                $exc = new MyEasyException("Missing required parameters ....", 400);
                $exc->setDetails("Please check Config/routes.php file for the requested url "
                        . "and the parameters in the action method of the respective "
                        . "controller.");
                throw ($exc);
            }
        }
        return $arguments;
    }
}
