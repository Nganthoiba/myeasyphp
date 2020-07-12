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
    private $queryBuilder;
    private $response;
    public function __construct() {
        $this->queryBuilder = new EasyQueryBuilder();
        $this->response = new Response();
    }
    
    /********* START METHODS FOR CRUD OPERATIONS ********/
    //Read records from a table
    /*
    public function read($table_name_or_entity) : EasyQueryBuilder{
        if($table_name_or_entity instanceof EasyEntity){
            return $this->readEntity($table_name_or_entity);
        }
        $this->queryBuilder->setEntityClassName($table_name_or_entity); 
        return $this->queryBuilder->select()->from($table_name_or_entity);        
    }
    */
    //Read records by passing entity object
    public function read(EasyEntity $entity) : EasyQueryBuilder{
        $this->queryBuilder->setEntityClassName(get_class($entity)); 
        return $this->queryBuilder->select()->from($entity->getTable());
    }
    //Entity Manager to read data from a table/relation
    public function readTable(string $table_name,$fields=[]): EasyQueryBuilder{
        /**
         * $table_name : name of the table 
         * $fields     : An array of columns in the table**/
        return $this->queryBuilder->select($fields)->from($table_name);
    }
    
    //Create or add a new (entity)record in the table
    public function add(EasyEntity $entity): Response{
        //$this->queryBuilder->setEntityClassName($entity->getTable());
        if(!$entity->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);
        }
        else{
            try{
                $data = ($entity->toArray());
                $stmt = $this->queryBuilder->insert($entity->getTable(), $data)->execute();
                
                if($entity->{$entity->getKey()}=="" || $entity->{$entity->getKey()}==null){
                    //$entity->{$entity->getKey()} = $stmt->lastInsertId();
                    $entity->{$entity->getKey()} = $this->queryBuilder::$conn->lastInsertId();
                }
                
                $this->response->set([
                    "msg" => "Record saved successfully.",
                    "status"=>true,
                    "status_code"=>200,
                    "data"=>$entity
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
    //terminology save will mean both insertion if record does not exist and updation if record already exist
    public function save(EasyEntity $entity): Response{
        if(!$entity->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);          
        }
        else{
            //check if entity already exist or not
            $temp_entity = $this->find($entity, $entity->{$entity->getKey()});
            $data = ($entity->toArray());
            try{
                if(is_null($temp_entity)){
                    //then go for inserting new record
                    $stmt = $this->queryBuilder->insert($entity->getTable(), $data)->execute();

                    if($entity->{$entity->getKey()}=="" || $entity->{$entity->getKey()}==null){
                        //$entity->{$entity->getKey()} = $stmt->lastInsertId();
                        $entity->{$entity->getKey()} = $this->queryBuilder::$conn->lastInsertId();
                    }

                    $this->response->set([
                        "msg" => "Record inserted successfully.",
                        "status"=>true,
                        "status_code"=>200,
                        "data"=>$entity
                    ]);
                }
                else{
                    //otherwise go for updating the entity
                    unset($data[$entity->getKey()]);//key will not be updated
                    $cond = [
                        //primary key attribute = value
                        $entity->getKey() => ['=',$entity->{$entity->getKey()}]
                    ];
                    $stmt = $this->queryBuilder
                            ->update($entity->getTable())
                            ->set($data)
                            ->where($cond)
                            ->execute();

                    $this->response->set([
                            "msg" => "Record saved successfully.",
                            "status"=>true,
                            "status_code"=>200,
                            "data"=>$entity
                        ]);
                }
            }
            catch (Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while updating the record. ".$e->getMessage(),
                        "status"=>false,
                        "status_code"=>500,
                        "error"=>$this->queryBuilder->getErrorInfo()
                    ]);
            }
            unset($temp_entity);
        }
        return $this->response;
    }
    
    //to update or save record
    public function update(EasyEntity $entity): Response{
        if(!$entity->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);          
        }
        else{
            //$this->queryBuilder->setEntityClassName($entity->getTable());
            try{
                $data = ($entity->toArray());
                unset($data[$entity->getKey()]);//key will not be updated
                $cond = [
                    //primary key attribute = value
                    $entity->getKey() => ['=',$entity->{$entity->getKey()}]
                ];
                $stmt = $this->queryBuilder
                        ->update($entity->getTable())
                        ->set($data)
                        ->where($cond)
                        ->execute();
                
                $this->response->set([
                        "msg" => "Record saved successfully.",
                        "status"=>true,
                        "status_code"=>200,
                        "data"=>$entity,
                        "rows_affected"=>$stmt->rowCount()
                    ]);
                $this->queryBuilder->clear();
            }catch(Exception $e){
                $this->response->set([
                        "msg" => "Sorry, an error occurs while updating the record. ",
                        "status"=>false,
                        "status_code"=>500,
                        "error"=>[
                            $e->getMessage(),
                            $this->queryBuilder->getErrorInfo()]
                    ]);
            }
        }
        return $this->response;
    }
    
    //to delete an entity (record)
    public function remove(EasyEntity $entity): Response{
        //$this->queryBuilder->setEntityClassName($entity->getTable());
        if(!$entity->isValidEntity()) {
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
                    $entity->getKey() => ['=',$entity->{$entity->getKey()}]
                ];
                $stmt = $this->queryBuilder
                        ->delete()
                        ->from($entity->getTable())
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
    
    /********** END CRUD OPERATIONS *********/
    
    //Find an entity with primary key attribute
    public function find(EasyEntity $entity,$id){
        //If Entity is not valid
        if(!$entity->isValidEntity()) {
            throw new Exception(get_class($entity)." is not a valid entity class, please make sure "
                    . "that you have set table name and primary key attribute of this entity.",500);
        }
        $stmt = $this->queryBuilder->select()->from($entity->getTable())->where([
            $entity->getKey() => ['=',$id]
        ])->execute();
        if($stmt->rowCount() == 0){
            return null;            
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($res as $col_name=>$val){
            $entity->{$col_name} = $val;
        }
        return $entity;
    }
    
    //find maximum value of a column/field in a table, the column should be of integer data type preferrably
    public function findMax($table,$column/*Column/Attribute name*/,$cond=array()){
        $stmt = $this->queryBuilder->select(" max(".$column.") as max_val")
                ->from($table)
                ->where($cond)
                ->execute();
        //$stmt = $this->read(" max(".$column.") as max_val")->execute();
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
}
