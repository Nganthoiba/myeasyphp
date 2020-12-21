<?php
require_once dirname(__DIR__) . '../Config/path.php';
require_once VENDOR_PATH. 'autoload.php';
require_once CONFIG_PATH. 'app.php';
require_once CONFIG_PATH. 'routes.php';
require_once LIBS_PATH. 'special_functions.php';
// object $router is instantiated in the Config/routes.php file

use MyEasyPHP\Libs\Dispatcher;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\MyEasyException;

try{
    require_once LIBS_PATH.DS.'special_functions.php';    
    date_default_timezone_set(Config::get('default_time_zone'));
    Dispatcher::dispatch($router);
}
catch(TypeError $error){
    $errorMessage = "Found an error in data type. Please check line number ".$error->getLine().
            " of the file ".$error->getFile(). ". ".$error->getMessage();
    
    $view = errorView(400,$errorMessage,$error->getTraceAsString());
    echo $view->render();
    /*echo "<pre>";
    print_r([
        'Line NO: '=>$error->getLine(),
        'File Name: '=>$error->getFile(),
        'Message'=> $error->getMessage(),
        'Error'=>$error->getTrace()
            ]);
    echo "</pre>"; */
}
catch(MyEasyException $e){
    $view = errorView($e->getCode(), $e->getMessage(),$e->getDetails());
    echo $view->render();
}
catch(Exception $e){
    $view = errorView($e->getCode(), $e->getMessage());
    echo $view->render();
}
