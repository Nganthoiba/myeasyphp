<?php
use MyEasyPHP\Libs\Config;
//Configuration set up file
Config::set("app_name", "MyEasyPHP");/*Name of the application*/
Config::set("site_name", "Welcome to MyEasyPHP");
Config::set("default_time_zone", "Asia/Kolkata");

Config::set('default_view_layout', 'default');
Config::set('default_controller', 'Default');
Config::set('default_action', 'index');

//Domain configuration
Config::set('host', '/MyEasyPHP');



/*** Database Configuration ***/

/*
    For setting database driver (DB_DRIVER). Use the followings:
*   1. mysql     :-  for MySql Database Server
*   2. pgsql     :-  for Postgres Database Server
*   3. sqlsrv    :-  for Microsoft SQL Database Server
*/
/*
//For connectin gto postgres database server
Config::set('DB_CONFIG', [
    "DB_HOST" => "localhost",
    "DB_PORT" => 5432,
    "DB_DRIVER"=>"pgsql", //Database driver
    "DB_NAME" => "easyphp",
    "DB_USERNAME" => "postgres",
    "DB_PASSWORD" => "postgres",//postgres
    "PERSISTENT" => false
]);
*/
//For connectin to Microsoft SQL database server
Config::set('DB_CONFIG', [
    "DB_HOST" => "localhost",
    "DB_PORT" => 1433,
    "DB_DRIVER"=>"sqlsrv", //Database driver
    "DB_NAME" => "easyphp",
    "DB_USERNAME" => "sa",
    "DB_PASSWORD" => "sa",//postgres
    "PERSISTENT" => false
]);
