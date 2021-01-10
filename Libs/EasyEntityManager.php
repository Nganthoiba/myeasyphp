<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;
/**
 * Description of EasyEntityManager:
 * 
 * The basic CRUDE operations than can be operated on an entity are defined in this 
 * class as follows:- 
 * 
 *      add     :-  for (C)creating/inserting a new (entity)record into a database table, 
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
use MyEasyPHP\Libs\EasyEntity;
use MyEasyPHP\Libs\EasyQueryBuilder;
use MyEasyPHP\Libs\Response;
use Exception;
use PDO;

class EasyEntityManager {
    public static $queryBuilder;
    private $response;
    public function __construct() {
        self::$queryBuilder = new EasyQueryBuilder();
        $this->response = new Response();
    }
    
    /********* START METHODS FOR CRUD OPERATIONS ********/
    
    //Read records by passing entity object
    public function read(EasyEntity $entity, array $fields=array()) : EasyQueryBuilder{
        self::$queryBuilder->setEntityClassName(get_class($entity)); 
        if(empty($fields)){
            $fields = $entity->getReadableFields();
        }
        return self::$queryBuilder->select($fields)->from($entity->getTable());
    }
    //Entity Manager to read data from a table/relation
    public function readTable(string $table_name,$fields=[]): EasyQueryBuilder{
        /**
         * $table_name : name of the table 
         * $fields     : An array of columns in the table**/
        return self::$queryBuilder->select($fields)->from($table_name);
    }
    
    //Create or add a new (entity)record in the table
    public function add(EasyEntity $entity = null): Response{
        //$this->queryBuilder->setEntityClassName($entity->getTable());
        if(is_null($entity) || !$entity->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set or entity is null.",
                "status"=>false,
                "status_code"=>400
            ]);
        }
        else{
            try{
                $entity = $this->removeUndefinedProperty($entity);
                $data = ($entity->toArray());
                $stmt = self::$queryBuilder->insert($entity->getTable(), $data)->execute();
                
                if($entity->{$entity->getKey()}=="" || $entity->{$entity->getKey()}==null){                    
                    $entity->{$entity->getKey()} = self::$queryBuilder::$conn->lastInsertId();
                }
                
                $this->response->set([
                    "data" => $entity,
                    "msg" => "Record inserted successfully.",
                    "status"=>true,
                    "status_code"=>201,
                    "rows_affected"=>$stmt->rowCount()
                ]);
            }catch(Exception $e){
                $this->response->set([
                    "msg" => "Sorry, an error occurs while saving the record.",
                    "status"=>false,
                    "status_code"=>500,
                    "sqlErrorCode" => self::$queryBuilder->getsqlErrorCode(),
                    "error"=>self::$queryBuilder->getErrorInfo()
                ]);
            }
        }
        return $this->response;
    }
    
    //terminology save will mean both insertion if record does not exist and updation if record already exist
    public function save(EasyEntity $entity = null): Response{
        if(is_null($entity) || !$entity->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set or entity is null.",
                "status"=>false,
                "status_code"=>400
            ]);          
        }
        else{
            $entity = $this->removeUndefinedProperty($entity);
            //check if entity already exist or not
            $temp_entity = $this->find($entity, $entity->{$entity->getKey()});
            $data = ($entity->toArray());
            try{
                if(is_null($temp_entity)){
                    //then go for inserting new record
                    $stmt = self::$queryBuilder->insert($entity->getTable(), $data)->execute();

                    if($entity->{$entity->getKey()}=="" || $entity->{$entity->getKey()}==null){
                        $entity->{$entity->getKey()} = self::$queryBuilder::$conn->lastInsertId();
                    }

                    $this->response->set([
                        "data" => $entity,
                        "msg" => "Record inserted successfully.",
                        "status"=>true,
                        "status_code"=>201,
                        "rows_affected"=>$stmt->rowCount()
                    ]);
                }
                else{
                    //otherwise go for updating the entity
                    unset($data[$entity->getKey()]);//key will not be updated
                    $cond = [
                        //primary key attribute = value
                        $entity->getKey() => ['=',$entity->{$entity->getKey()}]
                    ];
                    $stmt = self::$queryBuilder
                            ->update($entity->getTable())
                            ->set($data)
                            ->where($cond)
                            ->execute();

                    $this->response->set([
                            "data" => $entity,
                            "msg" => "Record saved successfully.",
                            "status"=>true,
                            "status_code"=>200,
                            "rows_affected"=>$stmt->rowCount()
                        ]);
                }
            }
            catch (Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while updating the record.",
                        "status"=>false,
                        "status_code"=>500,
                        "sqlErrorCode" => self::$queryBuilder->getsqlErrorCode(),
                        "error"=>self::$queryBuilder->getErrorInfo()
                    ]);
            }
            unset($temp_entity);
        }
        return $this->response;
    }
    
    //to update or save record
    public function update(EasyEntity $entity = null): Response{
        if(is_null($entity) || !$entity->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set or entity is null.",
                "status"=>false,
                "status_code"=>400
            ]);          
        }
        else{
            //$this->queryBuilder->setEntityClassName($entity->getTable());
            try{
                $entity = $this->removeUndefinedProperty($entity);
                $data = ($entity->toArray());
                unset($data[$entity->getKey()]);//key will not be updated
                $cond = [
                    //primary key attribute = value
                    $entity->getKey() => ['=',$entity->{$entity->getKey()}]
                ];
                $stmt = self::$queryBuilder
                        ->update($entity->getTable())
                        ->set($data)
                        ->where($cond)
                        ->execute();
                
                $this->response->set([
                        "data" => $entity,
                        "msg" => "Record updated successfully.",
                        "status"=>true,
                        "status_code"=>200,
                        "rows_affected"=>$stmt->rowCount()
                    ]);
                self::$queryBuilder->clear();
            }catch(Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while updating the record. ",
                        "status"=>false,
                        "status_code"=>500,
                        "sqlErrorCode" => self::$queryBuilder->getsqlErrorCode(),
                        "error"=>[
                            $e->getMessage(),
                            self::$queryBuilder->getErrorInfo()]
                    ]);
            }
        }
        return $this->response;
    }
    
    //to delete an entity (record)
    public function remove(EasyEntity $entity = null): Response{
        //$this->queryBuilder->setEntityClassName($entity->getTable());
        if(is_null($entity) || !$entity->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set or entity is null.",
                "status"=>false,
                "status_code"=>400
            ]);            
        }
        else{
            try{
                $cond = [
                    //primary key attribute = value
                    $entity->getKey() => ['=',$entity->{$entity->getKey()}]
                ];
                $stmt = self::$queryBuilder
                        ->delete()
                        ->from($entity->getTable())
                        ->where($cond)
                        ->execute();
                $this->response->set([
                        "msg" => "Record removed successfully.",
                        "status"=>true,
                        "status_code"=>200,
                        "rows_affected"=>$stmt->rowCount()
                    ]);
            }catch(Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while removing the record.",
                        "status"=>false,
                        "status_code"=>500,
                        "sqlErrorCode" => self::$queryBuilder->getsqlErrorCode(),
                        "error"=>self::$queryBuilder->getErrorInfo()
                    ]);
            }
        }
        return $this->response;
    }
    
    /********** END CRUD OPERATIONS *********/
    
    //Find an entity with primary key attribute
    public function find(EasyEntity $entity,$id){
        //If Entity is not valid
        if(!$entity->isValidEntity()) {
            throw new Exception(get_class($entity)." is not a valid entity class, please make sure "
                    . "that you have set table name and primary key attribute of this entity.",500);
        }
        $stmt = self::$queryBuilder->select($entity->getReadableFields())->from($entity->getTable())->where([
            $entity->getKey() => ['=',$id]
        ])->execute();
        if($stmt->rowCount() == 0){
            return null;            
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($res as $col_name=>$val){
            $entity->{$col_name} = $val;
        }
        /***Removing hidden fields***/
        foreach ($entity->getHiddenFields() as $field){
            if(!isset($res[$field])){
                unset($entity->{$field});
            }
        }
        return $entity;
    }
    
    //find maximum value of a column/field in a table, the column should be of integer data type preferrably
    public function findMax($table,$column/*Column/Attribute name*/,$cond=array()){
        $stmt = self::$queryBuilder->select(" max(".$column.") as max_val")
                ->from($table)
                ->where($cond)
                ->execute();
        if($stmt->rowCount() == 0){
            return NULL;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['max_val'];
    }
    
    //function to get query builder
    public function getQueryBuilder(): EasyQueryBuilder{
        return self::$queryBuilder;
    }
    
    //Transaction Controlling Methods
    //Transaction beginning
    public function beginTransaction(){
        return self::$queryBuilder->beginTransaction();
    }
    //Transaction rollback
    public function rollbackTransaction(){
        return self::$queryBuilder->rollbackTransaction();
    }
    
    //Committing a transaction 
    public function commitTransaction(){
        return self::$queryBuilder->commitTransaction();
    }
    
    public function getConnection(){
        return EasyQueryBuilder::$conn;/*self::$queryBuilder->getConnection();*/
    }
    
    /*
     * Security Feature:
     * 
     * Each property of an entity which has been declared public corresponds to each of the 
     * attribute of the database table. So, if an unknown new property which is not an attribute  
     * of the table has been set accidentally by mistake or intensionally, then there might
     * be an error while updating record or populalting a new record, because that property (column) 
     * does not exist in the table. So such property has to be removed before adding a new 
     * record or updating an existing record. Romoving of such unwanted or undefined property 
     * of an entity class is done by the function removeUndefinedProperty(). 
     * 
     */
    private function removeUndefinedProperty(EasyEntity $entity): EasyEntity{        
        $data = $entity->toArray();
        foreach ($data as $key=>$value){
            if(!property_exists(get_class($entity), $key)){
                //unsetting unwanted properties from the entity object
                unset($entity->{$key});
            }
        }
        return $entity;
    }
    
}
