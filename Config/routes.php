<?php
declare(strict_types=1);

use MyEasyPHP\Libs\Router;

/*
 * Important Note: Don't change variable name for $route because it is global for the
 * entire application, so it is used in other files also. Changing it may lead to errors
 * in the system.
 */
$router = new Router();//Don't change it
/******************************************************/

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

/*** Examples ****/
//Anything enclosed by curly braces is a parameter you are goind to pass. Go to Default controller and index action, see
//below how these parameters are being accessed
/*
 * Defining the root:
 * */
$router->addRoute("/", [
    "Controller" => "Default",
    "Action" => "home"
]);
$router->addRoute("/Contact", [
    "Controller" => "Default",
    "Action" => "contact"
]);
$router->addRoute("/Default/addPerson", [
    "Controller" => "Default",
    "Action" => "addPerson"
],'GET|POST');

//Listing of available routes
$router->addRoute("/show_routes", function(){
    global $router;
    return view('Shared/route',['routes'=>$router->getRoutes(),'app_name'=>'MyEasyPHP']);
});

//An example of grouping routes
//Routes for api
$router->group("/api",function($router){
    $router->addRoute('/',function(){
        return "Welcome to RESTful API";
    });
    $router->group("/persons",function($router){
        $router->addRoute("/", [
            "Controller" => "Persons",
            "Action" => "index"
        ],'GET|POST');
        $router->addRoute("/{id}", [
            "Controller" => "Persons",
            "Action" => "index"
        ],'GET|PUT|PATCH|DELETE');
    });
    $router->group("/users", function($router){
        $router->addRoute("/", [
            "Controller" => "Users",
            "Action" => "index"
        ],'GET|POST');
        $router->addRoute("/{id}", [
            "Controller" => "Users",
            "Action" => "index"
        ],'GET|PUT|PATCH|DELETE');
    });
});

$router->group("/Accounts", function(Router $router){
    $router->addRoute("/", [
        "Controller" => "Accounts",
        "Action" => "login"
    ],"GET|POST");
    $router->addRoute("/login", [
        "Controller" => "Accounts",
        "Action" => "login"
    ],"GET|POST");
    $router->addRoute("/register", [
        "Controller" => "Accounts",
        "Action" => "register"
    ],"GET|POST");
    $router->addRoute("/logout", [
        "Controller" => "Accounts",
        "Action" => "logout"
    ]);
});

/****** End Examples ******/






