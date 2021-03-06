<?php
/**
 * Description of Database
 *  This Database class is to connect database
 * @author Nganthoiba
 */
namespace MyEasyPHP\Libs\Database;
use MyEasyPHP\Libs\Config;
use PDO;
use Exception;
use MyEasyPHP\Libs\MyEasyException;

class Database {
    public static $conn_error;//Database connection error
    public static $db_server; //Database server name
    public static function connect($db_config = null): \PDO{
        self::$conn_error = "";
        /***** Retrieving Database Configurations *****/
        if($db_config == null){
            $db_config = env();//$_ENV;//Config::get("DB_CONFIG");
        }
        self::$db_server = $db_driver = $db_config["DB_DRIVER"];
        $db_host = $db_config["DB_HOST"];
        $db_port = $db_config["DB_PORT"]??"";
        $db_name = $db_config["DB_NAME"];
        $db_username = $db_config["DB_USERNAME"];
        $db_password = $db_config["DB_PASSWORD"];
        //$persistent = $db_config["PERSISTENT"]??false;
        
        /*Data Source Name, database connection string*/
        
        switch(self::$db_server ){
            case 'sqlsrv':
                if($db_port == ""){
                    $DSN = $db_driver.":Server=".$db_host.", Database=".$db_name.";";
                }
                else{
                    $DSN = $db_driver.":Server=".$db_host.",{$db_port};Database=".$db_name.";";
                }
                break;
            default:
                $DSN = $db_driver.":host=".$db_host.";dbname=".$db_name.";port=".$db_port;
        }
                
        try{
            $conn = new PDO(
                    $DSN, 
                    $db_username, 
                    $db_password,
                    /*[PDO::ATTR_PERSISTENT => $persistent]*/
                    );
            if(!$conn){
                self::$conn_error = "Database connection failed.";
                throw new Exception("Database connection failed.",503);
            }
            //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //$conn->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            return $conn;
        }catch(Exception $e){
            self::$conn_error = $e->getMessage();
            $easyExcp = new MyEasyException("Database connection failed.", $e->getCode());
            $easyExcp->setDetails($e->getMessage());
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            $easyExcp->setErrorFile($caller['file']);
            $easyExcp->setErrorLine($caller['line']);
            $easyExcp->httpCode = 503;
            throw $easyExcp;
        }
    }
    //closing connection
    public static function close(){
        self::$conn = null;
    }
}