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
        //Read records by passing entity object
    public function read(EasyEntity $entity) : EasyQueryBuilder{
        $class_name = str_replace(ENTITY_NAMESPACE,"",get_class($entity));
        $this->queryBuilder->setEntityClassName($class_name); 
        return $this->queryBuilder->select()->from($entity->getTable());
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
    
    //to update or save record
    public function save(EasyEntity $entity): Response{
        $this->queryBuilder->setEntityClassName($entity->getTable());
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
    public function find(EasyEntity $entity,$id): EasyEntity{
//        string $entity_class_name
//        $entity_class_name = ENTITY_NAMESPACE.$entity_class_name;
//        $entity = new $entity_class_name();
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
