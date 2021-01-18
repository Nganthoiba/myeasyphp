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
        global $router;
        //first read all the controller file name from the Controllers directory
        if ($dh = opendir(CONTROLLERS_PATH)){
            while (($file = readdir($dh)) !== false){
                if(strpos($file,"Controller.php")===false){
                    continue;
                }                
                $controllerName = str_replace("Controller.php","",$file); 
                $controllerClass = CONTROLLER_NAMESPACE.$controllerName.'Controller';
                $controller = new $controllerClass();
                $reflectionClass = new ReflectionClass($controller);
                foreach($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method){                    
                    foreach($method->getAttributes(Route::class) as $attribute){                        
                        $router->addRoute($attribute->newInstance()->url,[
                            "Controller"=>$controllerName,
                            "Action"=>$method->getName()
                        ],$attribute->newInstance()->methods);
                    }                    
                }
            }
            closedir($dh);
        }
    }
}
