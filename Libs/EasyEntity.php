<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;
/**
 * Description of EasyEntity
 * 
 * The basic CRUDE operations than can be operated on a database table are defined in this 
 * class as follows:- 
 * 
 *      add     :-  for (C)creating/inserting a new (entity)record into a table, 
 *      read    :-  for (R)reading/retrieving records form table, 
 *      save    :-  for (U)updating an (entity) existing record and 
 *      remove  :-  for (D)deleting a record(entity) from table
 * 
 *      All these methods has return type of Response class, except read method returns an
 *      object of EasyQueryBuilder class.
 * 
 * Other php files used:-
 *      EasyQueryBuilder.class.php
 *      Response.class.php
 * @author Nganthoiba
 */

use MyEasyPHP\Libs\EasyQueryBuilder;
use MyEasyPHP\Libs\Response;
use Exception;

class EasyEntity {
    private $table_name;//name of the table in the database
    private $key;//primary key
    private $queryBuilder;
    private $response;
    public function __construct() {
        /* Entity name should be same as the table name that exists in database */
        $class_name = get_class($this);//class name contains namespaces
        $paths = explode("\\", $class_name);
        $size = count($paths); 
        $this->table_name = $paths[$size-1];//by default, the table name is set same as that of the entity class name
        $this->queryBuilder = new EasyQueryBuilder();
        $this->queryBuilder->setEntityClassName($class_name);//by default
        $this->response = new Response();
    }
    //method to set table name of the enity
    protected function setTable($table_name){
        $this->table_name = $table_name;
        return $this;
    }    
    //method to set key of the enity
    protected function setKey($key){
        $this->key = $key;
        return $this;
    }
    
    //method to get table name of an entity
    public function getTable(){
        return $this->table_name;
    }
    //method to get key of the enity
    public function getKey(){
        return $this->key;
    }
    
    //method to get query builder
    public function getQueryBuilder():EasyQueryBuilder{
        return $this->queryBuilder;
    }
    
    /*Convert self object to array*/
    public function toArray(){
        return json_decode(json_encode($this),true);
    }
    
    /*** Check for valid Entity ***/
    public function isValidEntity():bool{
        //If table name and key is not set, then entity is invalid
        if(trim($this->table_name) == "" || trim($this->key) == "" || $this->table_name == "EasyEntity"){
            return false;//entity is invalid
        }
        return true;//entity is valid
    }
    
    /*** method to set data to an entity ***/
    public function setEntityData(array $data){
        $obj_data = $this->toArray();
        foreach($obj_data as $key=>$val){
            $this->{$key} = isset($data[$key])?$data[$key]:null;
        }
    }
    
    /********* START METHODS FOR CRUD OPERATIONS ********/
    //Creat or add a new record in the table
    public function add(): Response{   
        if(!$this->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);
        }
        else{
            try{
                $data = ($this->toArray());
                $stmt = $this->queryBuilder->insert($this->table_name, $data)->execute();
                if($this->{$this->getKey()}=="" || $this->{$this->getKey()}==null){
                    $entity->{$entity->getKey()} = $this->queryBuilder::$conn->lastInsertId();
                }
                $this->response->set([
                    "msg" => "Record saved successfully.",
                    "status"=>true,
                    "status_code"=>200,
                    "data"=>$this
                ]);
            }catch(Exception $e){
                $this->response->set([
                    "msg" => "Sorry, an error occurs while saving the record. ".$e->getMessage(),
                    "status"=>false,
                    "status_code"=>500,
                    "error"=>$this->queryBuilder->getErrorInfo()
                ]);
            }
        }
        return $this->response;
    }
    //to read record
    public function read($columns = array()): EasyQueryBuilder{
        return $this->queryBuilder->select($columns)->from($this->table_name);
    }
    //to update and save record
    public function save(): Response{
        if(!$this->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);          
        }
        else{
            try{
                $data = ($this->toArray());
                unset($data[$this->key]);//key will not be updated
                $cond = [
                    //primary key attribute = value
                    $this->key => ['=',$this->{$this->key}]
                ];
                $stmt = $this->queryBuilder
                        ->update($this->table_name)
                        ->set($data)
                        ->where($cond)
                        ->execute();
                
                $this->response->set([
                        "msg" => "Record updated successfully.",
                        "status"=>true,
                        "status_code"=>200,
                        "data"=>$this
                    ]);
                $this->queryBuilder->clear();
            }catch(Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while updating the record. ".$e->getMessage(),
                        "status"=>false,
                        "status_code"=>500,
                        "error"=>$this->queryBuilder->getErrorInfo()
                    ]);
            }
        }
        return $this->response;
    }
    
    //to delete record
    public function remove(): Response{
        if(!$this->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);            
        }
        else{
            try{
                $cond = [
                    //primary key attribute = value
                    $this->key => ['=',$this->{$this->key}]
                ];
                $stmt = $this->queryBuilder
                        ->delete()
                        ->from($this->table_name)
                        ->where($cond)
                        ->execute();
                $this->response->set([
                        "msg" => "Record removed successfully.",
                        "status"=>true,
                        "status_code"=>200
                    ]);
            }catch(Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while removing the record. ".$e->getMessage(),
                        "status"=>false,
                        "status_code"=>500,
                        "error"=>$this->queryBuilder->getErrorInfo()
                    ]);
            }
        }
        return $this->response;
    }
    
    /*** Entity Data Validation ***/
    //This method needs to be overridden in every extended entity class according to the purpose
    //otherwise this default validation method will be executed.
    public function validate(): Response{
        return $this->response->set([
                        "msg" => "Validated",
                        "status"=>true,
                        "status_code"=>200
                    ]);
    }
    
    
    /*** find an Entity ***/
    public function find($id){
        //If Entity is not valid
        if(!$this->isValidEntity()) {
            throw new Exception(get_class($this)." is not a valid entity class, please make sure "
                    . "that you have set table name and primary key attribute of this entity.",500);
        }
        $stmt = $this->queryBuilder->select()->from($this->table_name)->where([
            $this->key => ['=',$id]
        ])->execute();
        if($stmt->rowCount()==0){
            return null;            
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($res as $col_name=>$val){
            $this->{$col_name} = $val;
        }
        return $this;        
    }
    /********** END CRUD OPERATIONS *********/
    
    //find maximum value of a column, the column should be of integer data type preferrably
    public function findMaxColumnValue($column/*Column/Attribute name*/){
        $stmt = $this->queryBuilder->select(" max(".$column.") as max_val")
                ->from($this->getTable())
                ->execute();
        //$stmt = $this->read(" max(".$column.") as max_val")->execute();
        if($stmt->rowCount() == 0){
            return 0;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['max_val'] == NULL?0:is_numeric($row['max_val'])?(int)$row['max_val']:$row['max_val'];
    }
}