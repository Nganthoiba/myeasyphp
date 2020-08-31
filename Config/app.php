<?php
use MyEasyPHP\Libs\Config;

error_reporting(E_ALL);

/***
 * WARNING:
 * Don't try to remove any pre-existing configuration, but you can add yours.
 * The 'set' method of the class 'Config' has two parameters, the first one is the key
 * while the second is the value. Please don't try to change the key, but you can change the value.
 * 
 */
//Configuration set up file
Config::set("app_name", "MyEasyPHP");/*Name of the application*/
Config::set("site_name", "Welcome to MyEasyPHP");
Config::set("site_title", "Page Title");
Config::set("default_time_zone", "Asia/Kolkata");

Config::set('default_view_container', 'default');//default means default.view.php that exist inside View/Shared directory
Config::set('default_controller', 'Default');
Config::set('default_action', 'index');

//Domain configuration
Config::set('host', '/MyEasyPHP');
//Assets Configuration
Config::set('Assets', Config::get('host').'/Webroot/Assets');


/*** Database Configuration ***/

/*
    For setting database driver (DB_DRIVER). Use the followings:
*   1. mysql     :-  for MySql Database Server
*   2. pgsql     :-  for Postgres Database Server
*   3. sqlsrv    :-  for Microsoft SQL Database Server
*/

//For connectin gto postgres database server
Config::set('DB_CONFIG', [
    "DB_HOST" => "localhost",
    "DB_PORT" => 5432,
    "DB_DRIVER"=>"pgsql", //Database driver
    "DB_NAME" => "myeasyphp",
    "DB_USERNAME" => "postgres",
    "DB_PASSWORD" => "postgres",//postgres
    "PERSISTENT" => false
]);

////For connecting to Microsoft SQL database server
//Config::set('DB_CONFIG', [
//    "DB_HOST" => "localhost",
//    "DB_PORT" => 1433,
//    "DB_DRIVER"=>"sqlsrv", //Database driver
//    "DB_NAME" => "MyEasyPHP",
//    "DB_USERNAME" => "sa",
//    "DB_PASSWORD" => "sa",//postgres
//    "PERSISTENT" => false
//]);
//error Details display either true or false
//set it true during development time
Config::set('error_display',true);
