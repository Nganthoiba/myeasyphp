<?php
namespace MyEasyPHP\Libs\Database;

/**
 * Description of DbConnection
 * To support multiple database connection
 * @author Nganthoiba
 */
class DbConnection {
    private $conn;//by default null
    private $db_driver;
    private $db_name;
    private $db_host;
    private $db_port;
    private $db_username;
    private $db_password;

    public function __construct($dbDriver,$dbName, $host, $username, $password="", $port=""){
        $this->conn = null;
        $this->db_name = $dbName;
        $this->db_driver = $dbDriver;
        $this->db_host = $host;
        $this->db_port = $port;
        $this->db_username = $username;
        $this->db_password = $password;
    }
    
    public function getConnectionParameters():array{
        return [
            "DB_HOST" => $this->db_host,
            "DB_PORT" => $this->db_port,
            "DB_DRIVER"=> $this->db_driver, //Database driver
            "DB_NAME" => $this->db_name,
            "DB_USERNAME" => $this->db_username,
            "DB_PASSWORD" => $this->db_password,
        ];
    }

    public function getConnection(): \PDO{
        if(is_null($this->conn)){
            //if connection is null then establish a new connection
            $db_config = $this->getConnectionParameters();
            $this->conn = Database::connect($db_config);
        }
        return $this->conn;
    }
    
    public function closeConnection(){
        $this->conn = null;
    }
}
