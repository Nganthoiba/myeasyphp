<?php

require_once dirname(__DIR__) . '../Config/path.php';
require_once VENDOR_PATH. 'autoload.php';
require_once CONFIG_PATH. 'app.php';
require_once CONFIG_PATH. 'routes.php';
// object $router is instantiated in the Config/routes.php file

use MyEasyPHP\Libs\Dispatcher;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\MyEasyException;

try{
    require_once LIBS_PATH.DS.'special_functions.php';
    startSecureSession();
    date_default_timezone_set(Config::get('default_time_zone'));
    Dispatcher::dispatch($router);
}
catch(MyEasyException $e){
    $view = errorView($e->getCode(), $e->getMessage().$e->getDetails());
    echo $view->render();
    //echo http_response_code($e->getCode());
}
catch(Exception $e){
    $view = errorView($e->getCode(), $e->getMessage());
    echo $view->render();
}
