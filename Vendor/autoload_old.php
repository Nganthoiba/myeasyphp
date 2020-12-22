<?php
/*

 * The purpose of this file is to load all
 *  required php class files when an object is created.  */

try{
    spl_autoload_register(function($classname) {        
        // die($class_file);
        $classname = str_replace("MyEasyPHP\\", "", $classname);
        $class_file = ROOT.DS.$classname.'.php';
        $class_file = str_replace("\\",DS,$class_file);
        if(file_exists($class_file) || is_readable($class_file)){
            require_once ($class_file);
        }
        else{
            //throw new Exception("Class: ".$classname." does not exist. class-path: ".$class_file,404);
            throw new Exception("Sorry, the page you are looking for is not found.",404);
        } 
		
    });
}catch(Exception $e){
    throw $e;
}