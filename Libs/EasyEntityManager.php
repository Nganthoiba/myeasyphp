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
 *      update  :-  for (U)updating an (entity) existing record and 
 *      remove  :-  for (D)deleting a record(entity) from table
 *      save    :-  for both insertion if record does not exist and update if record already exist
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
    public $queryBuilder;
    private $response;
    public function __construct($dbConnectionName='Default') {
        /*By default a query builder object will connect to default database connection defined in Config/database.php*/
        if(is_null($dbConnectionName) || trim($dbConnectionName)===""){
            $dbConnectionName = 'Default';
        }
        try{
            $this->queryBuilder = new EasyQueryBuilder($dbConnectionName);
        }
        catch(MyEasyException $exception){
            $caller = array_shift(debug_backtrace());       
            $exception->setErrorFile($caller['file'])->setErrorLine($caller['line']);
            throw $exception;
        }
        $this->response = new Response();
    }
    
    /********* START METHODS FOR CRUD OPERATIONS ********/
    
    //Read records by passing entity object
    public function read(EasyEntity $entity, array $fields=array()) : EasyQueryBuilder{
        $this->queryBuilder->setEntityClassName(get_class($entity)); 
        if(empty($fields)){
            $fields = $entity->getReadableFields();
        }
        return $this->queryBuilder->select($fields)->from($entity->getTable());
    }
    //Entity Manager to read data from a table/relation
    public function readTable(string $table_name,$fields=[]): EasyQueryBuilder{
        /**
         * $table_name : name of the table 
         * $fields     : An array of columns in the table**/
        return $this->queryBuilder->select($fields)->from($table_name);
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
                $stmt = $this->queryBuilder->insert($entity->getTable(), $data)->execute();
                
                $keys = $entity->getKeys();
                if(\sizeof($keys)===1){ 
                    if($entity->{$keys[0]}=="" || $entity->{$keys[0]}==null){                    
                        $entity->{$keys[0]} = $this->queryBuilder->getConnection()->lastInsertId();
                    }
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
                    "sqlErrorCode" => $this->queryBuilder->getsqlErrorCode(),
                    "error"=>$this->queryBuilder->getErrorInfo()
                ]);
            }
        }
        return $this->response;
    }
    
    //Method save will work for both insertion if record does not exist and updation if record already exist
    public function save(EasyEntity $entity = null): Response{        
        $entity = $this->removeUndefinedProperty($entity);
        //check if entity already exist or not          
        if(is_null($this->find($entity, $entity->getKeyConditions()))){                    
           return $this->add($entity);
        }
        return $this->update($entity);
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
            
            try{
                $entity = $this->removeUndefinedProperty($entity);
                $data = ($entity->toArray());
                $keys = $entity->getKeys();
                foreach($keys as $key){
                    unset($data[$key]);//key will not be updated
                }
                $cond = $entity->getKeyConditions();
                $stmt = $this->queryBuilder
                        ->update($entity->getTable(),$data)
                        ->where($cond)
                        ->execute();
                
                $this->response->set([
                        "data" => $entity,
                        "msg" => "Record updated successfully.",
                        "status"=>true,
                        "status_code"=>200,
                        "rows_affected"=>$stmt->rowCount()
                    ]);
                $this->queryBuilder->clear();
            }catch(Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while updating the record. ",
                        "status"=>false,
                        "status_code"=>500,
                        "sqlErrorCode" => $this->queryBuilder->getsqlErrorCode(),
                        "error"=>[
                            $e->getMessage(),
                            $this->queryBuilder->getErrorInfo()]
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
                $cond = $entity->getKeyConditions();
                $stmt = $this->queryBuilder
                        ->delete($entity->getTable())
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
                        "sqlErrorCode" => $this->queryBuilder->getsqlErrorCode(),
                        "error"=>$this->queryBuilder->getErrorInfo()
                    ]);
            }
        }
        return $this->response;
    }
    
    /********** END CRUD OPERATIONS *********/
    
    //Find an entity with primary key attribute
    public function find(EasyEntity $entity,$keyValuePairs = array()){
        $entityClass = get_class($entity);
        $tempEntity = new $entityClass();
        //If Entity is not valid
        if(!$entity->isValidEntity()) {
            throw new Exception(get_class($entity)." is not a valid entity class, please make sure "
                    . "that you have set table name and primary key attribute of this entity.",500);
        }
        $cond = is_array($keyValuePairs)?$keyValuePairs:[
            //only if $keyValuePairs is just a single value
            $entity->getKeys()[0] => ['=',$keyValuePairs]
        ];
        $stmt = $this->queryBuilder->select($entity->getReadableFields())
                ->from($entity->getTable())
                ->where($cond)->execute();
        if($stmt->rowCount() == 0){
            return null;            
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($res as $col_name=>$val){
            $tempEntity->{$col_name} = $val;
        }
        /***Removing hidden fields***/
        foreach ($entity->getHiddenFields() as $field){
            if(!isset($res[$field])){
                unset($tempEntity->{$field});
            }
        }
        return $tempEntity;
    }
    
    //find maximum value of a column/field in a table, the column should be of integer data type preferrably
    public function findMax($table,$column/*Column/Attribute name*/,$cond=array()){
        $stmt = $this->queryBuilder->select(" max(".$column.") as max_val")
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
        return $this->queryBuilder;
    }
    
    //Transaction Controlling Methods
    //Transaction beginning
    public function beginTransaction(){
        return $this->queryBuilder->beginTransaction();
    }
    //Transaction rollback
    public function rollbackTransaction(){
        return $this->queryBuilder->rollbackTransaction();
    }
    
    //Committing a transaction 
    public function commitTransaction(){
        return $this->queryBuilder->commitTransaction();
    }
    
    public function getConnection(){
        return $this->queryBuilder->getConnection();
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
