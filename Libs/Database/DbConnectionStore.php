<?php
namespace MyEasyPHP\Libs\Database;

/**
 * Description of DbConnectionStore
 * We introduce a class called DbConnectionStore which holds multiple database connection properties
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\MyEasyException;

class DbConnectionStore {
    public static $dbConnections = [];
    public static function getConnection($connectionName){
        if(!isset(self::$dbConnections[$connectionName])){
            $exception = new MyEasyException("Database connection name {$connectionName} does not exist.");
            $exception->setDetails("Make sure that you have set the database configuration for that connection name.");
            $exception->httpCode = 500;
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            $exception->setFile($caller['file']);
            $exception->setLine($caller['line']);
            throw $exception;
        }
        
        $conn = self::$dbConnections[$connectionName];
        return $conn->getConnection();
    }
    public static function getConnectionParameters($connectionName){
        if(!isset(self::$dbConnections[$connectionName])){
            $exception = new MyEasyException("Database connection name {$connectionName} does not exist.");
            $exception->setDetails("Make sure that you have set the database configuration for that connection name.");
            $exception->httpCode = 500;
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            $exception->setFile($caller['file']);
            $exception->setLine($caller['line']);
            throw $exception;
        }
        
        $conn = self::$dbConnections[$connectionName];
        return $conn->getConnectionParameters();
    }
    
    public static function addConnection($connectionName, $params=array()){
        $missingParams = self::getMissingParameters($params);
        if(sizeof($missingParams)!==0){ 
            $details = "Missing parameter(s) : ".implode(', ', $missingParams);
            $exception = new MyEasyException("Missing database connection parmeters for the connection name {$connectionName}. ".$details);
            
            $exception->setDetails($details);
            $exception->httpCode = 500;
            
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            $exception->setFile($caller['file']);
            $exception->setLine($caller['line']);
            
            throw $exception;
        }
        $db_driver = $params["DB_DRIVER"];
        $db_host = $params["DB_HOST"];
        $db_port = $params["DB_PORT"]??"";
        $db_name = $params["DB_NAME"];
        $db_username = $params["DB_USERNAME"];
        $db_password = $params["DB_PASSWORD"]??"";
        
        self::$dbConnections[$connectionName] = new DbConnection($db_driver, $db_name, $db_host, $db_username, $db_password, $db_port);        
        
    }
    //to close a connection
    public static function closeConnection($connectionName){
        if(!isset(self::$dbConnections[$connectionName])){
            $exception = new MyEasyException("Database connection name {$connectionName} does not exist.");
            $exception->setDetails("Make sure that you have set the database configuration for that connection name.");
            $exception->httpCode = 404;
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            $exception->setFile($caller['file']);
            $exception->setLine($caller['line']);
            throw $exception;
        }
        
        $conn = self::$dbConnections[$connectionName];
        $conn->closeConnection();
    }
    
    //To validate connection parameters
    private static function getMissingParameters($params = array()):array{
        $missingParams = [];
        if(!isset($params['DB_DRIVER']) || trim($params['DB_DRIVER']) === ""){
            $missingParams[] = 'DB_DRIVER';
        }
        if(!isset($params['DB_HOST']) || trim($params['DB_HOST']) === ""){
            $missingParams[] = 'DB_HOST';
        }
        if(!isset($params['DB_NAME']) || trim($params['DB_NAME']) === ""){
            $missingParams[] = 'DB_NAME';
        }
        if(!isset($params['DB_USERNAME']) || trim($params['DB_USERNAME']) === ""){
            $missingParams[] = 'DB_USERNAME';
        }
        if(!isset($params['DB_PASSWORD'])){
            $missingParams[] = 'DB_PASSWORD';
        }
        return $missingParams;
    }
}
