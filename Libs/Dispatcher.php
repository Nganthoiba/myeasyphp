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
use MyEasyPHP\Libs\Router;
use MyEasyPHP\Libs\Request;
use MyEasyPHP\Libs\Config;
use Exception;
use MyEasyPHP\Libs\MyEasyException;
use MyEasyPHP\Libs\Authorization;
use MyEasyPHP\Libs\ApiController;
use ReflectionMethod;
class Dispatcher {
    public static $router;
    public static $request;
    public static function getRouter(){
        return self::$router;
    }
    public static function dispatch(Router $router){
        //Getting user request information
        self::$request = new Request();        
        
        //Getting uri requested by user
        $uri = Request::getURI();
		
        if(!is_null($uri)){
            $uri = (rtrim($uri,'/'));
        }
        self::$router = $router;
        self::$router->setUri($uri);
        self::$router->extractComponents();//extract Controller and action wrt the request uri from the routes
        $methods = self::$router->getMethods();//getting HTTP verbs       
        //senitising all input values via GET or POST methods
        $params = self::$request->senitizeInputs(self::$router->getParams());       
        
        //If router is only a function
        if(self::$router->isOnlyFunction()){
            if(!in_array(self::$request->getMethod(), $methods)){                    
                $exc = new MyEasyException("Method not allowed.",405);
                $exc->setDetails("Methods allowed for the route '".self::$router->getRouteUrl()."' :- ".implode(', ',$methods)." but your request method is ".self::$request->getMethod());
                throw $exc;
            }
            $function = self::$router->getFunction();
            if(sizeof($params)>0){
                //executing the function
                $res = call_user_func_array($function, array_values($params));
            }else{
                $res = $function();//executing the function
            }
            if(!is_null($res)){
                echo $res;
            }
        }
        else{            
            $controller = is_null(self::$router->getController())?"Controller":ucfirst(self::$router->getController())."Controller";
            $action = self::$router->getAction();//Action name
            if(is_null($action)){
                $action = Config::get('default_action');
            }
			
            //*** creating Controller Object ***
            $controller_class = "MyEasyPHP\\Controllers\\".$controller;
            if(!class_exists($controller_class, TRUE)){
                $exception = new MyEasyException("Sorry, the page you are looking for is not found.",404);
                $exception->setDetails("Controller file ".$controller." class does not exist. Please check.");
                throw $exception;
            }
            $controllerObj = new $controller_class();//instantiate a new controller object
            $controllerObj->setRouter(self::$router);//very much necessary
            $controllerObj->setRequest(self::$request);//very much necessary
            $controllerObj->setParams($params);//setting parameters is very much necessary
            
            //Here check whether the controller is an object of ApiController or just normal Controller
            if(($controllerObj instanceof ApiController)){
                //checking whether the request method is allowed for accessing URI(For security)
                
                if(!in_array(self::$request->getMethod(), $methods)){                    
                    $resp = $controllerObj->response->set([
                        "status"=>false,
                        "status_code"=>405,
                        "error"=>"Methods allowed for the route '".self::$router->getRouteUrl()."' are: ".implode(', ',$methods).", but your request method is ".self::$request->getMethod()
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
                $reflection = new ReflectionMethod($controllerObj, $action);
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
                    $exc->setDetails("Methods allowed for the route '".self::$router->getRouteUrl()."' :- ".implode(', ',$methods)." but your request method is ".self::$request->getMethod());
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
                $reflection = new ReflectionMethod($controllerObj, $action);
                //ensuring only public method or action to be allowed to access otherwise
                //access will be denied.
                if (!$reflection->isPublic()) {
                    throw new MyEasyException("Access denied.",403);
                }
                //If the controller is not an api controller
                startSecureSession();
            }
			
            try{
                ///checking whether parameter exists or not
                if(sizeof($params)>0){
                    $view = call_user_func_array([$controllerObj,$action], array_values($params));
                }
                else{
                    $view = $controllerObj->$action();
                }
                //Controller Action may returns view or json data depending upon whether the controller is api controller or just controller, and it is going to be printed
                if(is_null($view)){
                    //echo "Null";
                } 
                else if(is_object($view) && $view instanceof View){                           
                    //if it is view object then render its contents
                    header('X-Frame-Options: SAMEORIGIN');//preventing clickjacking as the page can only be displayed in a frame on the same origin as the page itself. 
                    //header('X-Frame-Options: deny');//The page cannot be displayed in a frame, regardless of the site attempting to do so.
                    echo $view->render(); 
                }
                else{
                    echo ($view);            
                }
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
}
