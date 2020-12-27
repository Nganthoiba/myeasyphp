<?php
require_once dirname(__DIR__) . '../Config/path.php';//path configuration
require_once VENDOR_PATH. 'autoload.php';
require_once CONFIG_PATH. 'app.php';// Application configuration file
require_once LIBS_PATH.'global_variables.php';//loading global variables
require_once CONFIG_PATH. 'routes.php';//Route Configuration file
require_once LIBS_PATH. 'special_functions.php';

// object $router is instantiated in the Config/routes.php file
use MyEasyPHP\Libs\Dispatcher;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\MyEasyException;

try{
    require_once LIBS_PATH.DS.'special_functions.php';    
    date_default_timezone_set(Config::get('default_time_zone'));
    Dispatcher::dispatch();
}
catch(Error $error){
    $errorDetails = "Please check line number ".$error->getLine().
            " of the file ".$error->getFile(). ". <br/>".$error->getTraceAsString();
    http_response_code(500);
    $view = errorView(500,$error->getMessage(),$errorDetails);
    echo $view->render();
}
catch(TypeError $error){
    $errorDetails = "Found an error in data type. Please check line number ".$error->getLine().
            " of the file ".$error->getFile(). ". <br/>".$error->getTraceAsString();
    http_response_code(500);
    $view = errorView(500,$error->getMessage(),$errorDetails);
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
