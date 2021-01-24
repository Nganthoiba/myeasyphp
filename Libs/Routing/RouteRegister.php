<?php
namespace MyEasyPHP\Libs\Routing;

/**
 * Description of RouteRegister
 * The purpose of this class is to collect all the routes defined in controller class
 * just above the action method with the help of attributes and then register those 
 * routes in the router objects.
 * @author Nganthoiba
 */
use ReflectionClass;
use ReflectionMethod;
use MyEasyPHP\Libs\Attributes\Route;

class RouteRegister {
    public static function collectRoutesAndRegister(){
        //first read all the controller file name from the Controllers directory
        $dh = opendir(CONTROLLERS_PATH);
        if ($dh){
            while (($file = readdir($dh)) !== false){
                if(strpos($file,"Controller.php")===false){
                    continue;
                }                
                $controllerName = str_replace("Controller.php","",$file); 
                self::setRoutes($controllerName);
            }
            closedir($dh);
        }
    }
    
    private static function setRoutes(string $controllerName){
        global $router;
        $controllerClass = CONTROLLER_NAMESPACE.$controllerName.'Controller';
        $controller = new $controllerClass();
        $reflectionClass = new ReflectionClass($controller);
        foreach($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
            foreach($method->getAttributes(Route::class) as $attribute){
                $attributeInstance = $attribute->newInstance();
                $router->addRoute($attributeInstance->url,[
                    "Controller"=>$controllerName,
                    "Action"=>$method->getName()
                ],$attributeInstance->methods);
            }
        }
    }    
}
