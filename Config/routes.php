<?php
declare(strict_types=1);

use MyEasyPHP\Libs\Router;
$router = new Router();

/*
 * Reference: https://www.codediesel.com/php/how-do-mvc-routers-work/
 * $router = new Router();
 * $router->addRoute(param1,param2,param3[optional]);
 * The first parameter is the request uri or url, and the second parameter can be just a function
 * or an associative array indicating which controller and what action to be called.
 * and lastly the third parameter is the methods(http verbs) which is allowed for the request url, its values 
 * should be passed in the form of array like ['POST','PUT'] or in the string format separated by | character
 * like "POST|PUT", this third parameter is optional, if you don't pass, by default its value is GET, which means 
 * the route is accessible by GET method. Below is a list of exxamples:
 */

/*
 * Defining the root:
 * 
$router->addRoute("/", function(){
    echo "Hello World! This is root";
});
*/

/*** Examples ****/
//Anything enclosed by curly braces is a parameter you are goind to pass. Go to Default controller and index action, see
//how these parameters are being accessed
/*
$router->addRoute("/api/hello/{fname}/{lname}", [
    "Controller" => "Default",
    "Action" => "hello"
],"GET");
*/
$router->addRoute("/Default/sum/{num1}/{num2}", [
    "Controller" => "Default",
    "Action" => "sum"
],"GET");
$router->addRoute("/Default/product/{num1}/{num2}", [
    "Controller" => "Default",
    "Action" => "product"
]);
$router->addRoute("/Default/test/{script}", [
    "Controller" => "Default",
    "Action" => "test"
],"GET");

$router->addRoute("/api/sum/{num1}/{num2}", function($num1,$num2){    
    echo "sum is : ".($num1+$num2);
});

$router->addRoute("/api/contacts", [
    "Controller" => "Default",
    "Action" => "contact"
],"POST|GET");
/****** End Examples ******/

$router->addRoute("/", [
    "Controller" => "Default",
    "Action" => "index"
],"GET");
$router->addRoute("/Accounts/login", [
    "Controller" => "Accounts",
    "Action" => "login"
],"GET|POST");
$router->addRoute("/Accounts/register", [
    "Controller" => "Accounts",
    "Action" => "register"
],"GET|POST");






