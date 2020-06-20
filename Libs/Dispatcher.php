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
        $uri = filter_input(INPUT_GET, "uri", FILTER_SANITIZE_SPECIAL_CHARS);
        if(!is_null($uri)){
            $uri = rtrim($uri,'/');
        }
        self::$router = $router;
        self::$router->setUri($uri);
        self::$router->extractComponents();//extract Controller and action wrt the request uri from the routes
        
        
        $params = self::$request->senitizeInputs(self::$router->getParams());
        //If router is only a function
        if(self::$router->isOnlyFunction()){
            $function = self::$router->getFunction();
            if(sizeof($params)>0){
                $function($params);//executing the function
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
            
            $methods = self::$router->getMethods();//HTTP verbs
            //checking whether the request method is allowed for routing URI
            if(!in_array(self::$request->getMethod(), $methods)){
                throw new Exception("Method not allowed",403);//forbidden
            }
            /*** creating Controller Object ***/
            $controller_class = "MyEasyPHP\\Controllers\\".$controller;
            $controllerObj = new $controller_class();
            if(!method_exists($controllerObj, $action)){
                throw new Exception("Action '".$action."' of controller class '"
                        .$controller."' does not exist.",500);
            }

            $controllerObj->setRouter(self::$router);//very much necessary
            $controllerObj->setRequest(self::$request);//very much necessary
            
            ///checking whether parameter exists or not
            if(sizeof($params)>0){
                $view = $controllerObj->$action($params);
            }
            else{
                $view = $controllerObj->$action();
            }
            
            //Controller Action may returns view or json data depending upon whether the controller is api controller or just controller, and it is going to be printed
            if(is_object($view) && $view instanceof View){                           
                //if it is view object then render its contents
                echo $view->render();                
            }
            else{
                echo ($view);            
            }
            
        }
        
    }
}
