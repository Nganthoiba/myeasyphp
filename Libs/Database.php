<?php
/**
 * Description of Database
 *  This Database class is to connect database
 * @author Nganthoiba
 */
namespace Libs;
use Libs\Config;
use PDO;
use Exception;

class Database {
    public static $conn_error;//Database connection error
    public static $db_server; //Database server name
    public static function connect($db_config = null){
        self::$conn_error = "";
        /***** Retrieving Database Configurations *****/
        if($db_config == null){
            $db_config = Config::get("DB_CONFIG");
        }
        self::$db_server = $db_driver = $db_config["DB_DRIVER"];
        $db_host = $db_config["DB_HOST"];
        $db_port = $db_config["DB_PORT"]??"";
        $db_name = $db_config["DB_NAME"];
        $db_username = $db_config["DB_USERNAME"];
        $db_password = $db_config["DB_PASSWORD"];
        $persistent = isset($db_config["PERSISTENT"])?$db_config["PERSISTENT"]:false;
        
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
                    [PDO::ATTR_PERSISTENT => $persistent]
                    );
            if(!$conn){
                self::$conn_error = "Database connection failed.";
                throw new Exception("Database connection failed.",503);
            }
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            return $conn;
        }catch(Exception $e){
            self::$conn_error = $e->getMessage();
            throw $e;
        }
        return null;
    }
    //closing connection
    public static function close(){
        self::$conn = null;
    }
}