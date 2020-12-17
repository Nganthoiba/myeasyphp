<?php
declare(strict_types=1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Libs;

/**
 * Description of Dispatcher
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Router;
use MyEasyPHP\Libs\Request;
use MyEasyPHP\Libs\Config;
use Exception;
use MyEasyPHP\Libs\MyEasyException;
use MyEasyPHP\Libs\Authorization;
use PDO;
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
        //checking whether the request method is allowed for routing URI(For security)
        if(!in_array(self::$request->getMethod(), $methods)){
            $exc = new MyEasyException("Method not allowed.",405);//forbidden
            $exc->setDetails("Methods allowed for the route:- ".self::$router->getRouteUrl()." are: ".json_encode($methods).", but your request method is ".self::$request->getMethod());
            throw $exc;            
        }        
        //senitising all input values via GET or POST methods
        $params = self::$request->senitizeInputs(self::$router->getParams());       
        
        //If router is only a function
        if(self::$router->isOnlyFunction()){
            $function = self::$router->getFunction();
            if(sizeof($params)>0){
                //$function($params);//executing the function
                call_user_func_array($function, array_values($params));
            }else{
                $function();//executing the function
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
            $controllerObj = new $controller_class();
            if(!method_exists($controllerObj, $action)){
                throw new Exception("The page you are looking for does not exist. Action '".$action."' of controller class '"
                        .$controller."' does not exist.",404);
            }
            			
			
            if(!Authorization::isAuthorized($controllerObj,$action)){
                $exc = new Exception("Unauthorize access. You are not allowed to access the page. <a href='".Config::get('host')."/Accounts/login'>Login</a> with "
                        . "an authorized account. ",403);//forbidden                
                throw $exc;
            }
						
            $controllerObj->setRouter(self::$router);//very much necessary
            $controllerObj->setRequest(self::$request);//very much necessary
			
            try{
                ///checking whether parameter exists or not
                if(sizeof($params)>0){
                        //$view = $controllerObj->$action($params);
                        $view = call_user_func_array([$controllerObj,$action], array_values($params));
                }
                else{
                        $view = $controllerObj->$action();
                }
                //Controller Action may returns view or json data depending upon whether the controller is api controller or just controller, and it is going to be printed
                if(is_null($view)){
                        echo "Null";
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
            }catch(Exception $e){
                throw $e;
                //echo $e->getMessage();
            }
        }
        
    }
}
