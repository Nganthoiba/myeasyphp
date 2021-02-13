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
Config::set('host', '');
//Assets Configuration
Config::set('Assets', Config::get('host').'/Assets');

//error Details display either true or false
//set it true during development time
//Debugging is granted only if error_display is set to true.
//This flag must be set to true only when development is in progress.
//Beware that, when the code is in production server, it must be set
//to false.
Config::set('error_display',true);
Config::set('development_mode',true);
