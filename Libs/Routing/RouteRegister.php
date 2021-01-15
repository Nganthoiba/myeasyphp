<?php
namespace MyEasyPHP\Libs\Routing;

/**
 * Description of RouteRegister
 * The purpose of this class is to collect all the routes defined in controller class
 * with the help of attributes and then register those routes in the router objects
 * @author Nganthoiba
 */
use ReflectionClass;
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
                foreach($reflectionClass->getMethods() as $method){
                    if($method->isPublic()){
                        foreach($method->getAttributes() as $attribute){
                            if(basename($attribute->getName())==="Route"){
                                $router->addRoute($attribute->newInstance()->url,[
                                    "Controller"=>$controllerName,
                                    "Action"=>$method->getName()
                                ],$attribute->newInstance()->methods);
                            }
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}
