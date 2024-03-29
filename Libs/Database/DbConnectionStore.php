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
    
    //This function returns PDO connection, so that it can be used normally for row sql query execution 
    public static function getConnection($connectionName):\PDO{
        if(!isset(self::$dbConnections[$connectionName])){
            $exception = new MyEasyException("Database connection name {$connectionName} does not exist.");
            $exception->setDetails("Make sure that you have set the database configuration for the connection name '{$connectionName}'.");
            $exception->httpCode = 500;
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            $exception->setErrorFile($caller['file'])->setErrorLine($caller['line']);
            throw $exception;
        }
        try{
            $conn = self::$dbConnections[$connectionName];
            return $conn->getConnection();
        }catch(MyEasyException $exception){
            $exception->addDetail("Please check database configuration for {$connectionName}");
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            $exception->setErrorFile($caller['file'])->setErrorLine($caller['line']);
            throw $exception;
        }
        
    }
    public static function getConnectionParameters($connectionName){
        if(!isset(self::$dbConnections[$connectionName])){
            $exception = new MyEasyException("Database connection name {$connectionName} does not exist.");
            $exception->setDetails("Make sure that you have set the database configuration for "
                    . "the connection name '{$connectionName}'.");
            $exception->httpCode = 500;
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            $exception->setErrorFile($caller['file']);
            $exception->setErrorLine($caller['line']);
            throw $exception;
        }
        
        $conn = self::$dbConnections[$connectionName];
        return $conn->getConnectionParameters();
    }
    
    public static function addConnection($connectionName, $params=array()){
        $missingParams = self::getMissingParameters($params);
        if(sizeof($missingParams)!==0){ 
            $details = "Following parameter(s) are required: ".implode(', ', $missingParams);
            $exception = new MyEasyException("Some database connection parmeters are either missing or "
                    . "have empty string or blank for the connection name {$connectionName}. ");
            
            $exception->setDetails($details);
            $exception->addDetail("Make sure that those parameters are set in the database configuration files "
                    . "both in the config/database.php as well as .env file, and their "
                    . "values must not be empty string.");
            $exception->httpCode = 500;
            
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            $exception->setErrorFile($caller['file']);
            $exception->setErrorLine($caller['line']);
            
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
            
            $exception->setErrorFile($caller['file'])->setErrorLine($caller['line']);
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
