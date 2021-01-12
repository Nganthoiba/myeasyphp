<?php
require_once dirname(__DIR__) . '../Config/path.php';//path configuration
require_once VENDOR_PATH. 'autoload.php';
require_once LIBS_PATH.'global_variables.php';//loading global variables
require_once LIBS_PATH. 'special_functions.php';//loading global functions
use MyEasyPHP\Libs\Dispatcher;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\MyEasyException;
//Load ENV parser
use Dotenv\Dotenv;
set_error_handler("handleMyEasyPHPError");
/*
 * handleMyEasyPHPError is a php function defined in file special_functions.php 
 */
try{
    $env = Dotenv::createUnsafeImmutable(ROOT);
    $env->load();
    require_once CONFIG_PATH. 'app.php';// Application configuration file
    require_once CONFIG_PATH. 'routes.php';//Route Configuration file 
    date_default_timezone_set(Config::get('default_time_zone'));    
    Dispatcher::dispatch();
}
catch(Error $error){
    $errorDetails = $error->getTraceAsString();
    $errorMsg = "Please check line number ".$error->getLine().
            " of the file ".$error->getFile(). ". ";
    http_response_code(500);
    $view = errorView(500,$error->getMessage().". ".$errorMsg,$errorDetails);
    echo $view->render();
}
catch(TypeError $error){
    $errorDetails = $error->getTraceAsString();
    http_response_code(500);
    $errorMsg = "Found an error in data type. Please check line number ".$error->getLine().
            " of the file ".$error->getFile(). ".";
    $view = errorView(500,$error->getMessage().". ".$errorMsg,$errorDetails);
    echo $view->render();
}
catch(MyEasyException $e){
    http_response_code($e->getCode());
    $view = errorView($e->getCode(), $e->getMessage(),$e->getDetails());
    echo $view->render();
}
catch(Exception $e){
    http_response_code(500);
    $view = errorView(500, $e->getMessage());
    echo $view->render();
}