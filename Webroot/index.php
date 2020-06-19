<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '../Config/path_config.php';
require_once VENDOR_PATH. 'autoload.php';
require_once CONFIG_PATH. 'app_config.php';
require_once CONFIG_PATH. 'routes.php';

// object $router is instantiated in the Config/routes.php file

use MyEasyPHP\Libs\Dispatcher;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Libs\ViewData;

try{
    require_once LIBS_PATH.DS.'special_functions.php';
    startSecureSession();
    date_default_timezone_set(Config::get('default_time_zone'));
    
    Dispatcher::dispatch($router);
}catch(Exception $e){
    $error_code = $e->getCode();
    $detail = $e->getMessage();
    $error = new ViewData(array("content"=>"Error: ".$error_code,"detail"=>$detail));
    
    $controller = new Controller($error);
    $controller->setRouter($router);//very much necessary    
    
    echo $controller->error()->render();//rendering view
}


