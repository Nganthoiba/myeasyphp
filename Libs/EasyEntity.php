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
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Response;
use PDO;
use Exception;

class EasyEntity extends Model{
    private $table_name;//name of the table in the database
    private $key;//primary key
    private $queryBuilder;
    private $response;
    /*
     * $hiddenFields is an array or set of attributes/fields in the database table which will be excluded
     * from retrieving records from the database table. Empty array will mean that all records 
     * for all the fields are to be retrieved, and no fields will be excluded.
     * 
     * This is important because when we don't want to disclose data for some specific attributes/fields/columns 
     * for example, password field and other security related informations, we can hide/exclude those 
     * attributes/columns/fields from showing to end users, by setting those fields in the variable $hiddenFields 
     * in the form of array.
     */
    protected $hiddenFields = [];
    
    public function __construct() {
        /*By convention, an entity class name should be same as the table name 
         * that exists in database, otherwise you have to override the constructor and 
         * use $this->setTable() method to set table name */
        $class_name = get_class($this);//class name contains namespaces        
        $this->table_name = basename($class_name);//by default, the table name is set 
        //same as that of the entity class name
        $this->queryBuilder = new EasyQueryBuilder();
        $this->queryBuilder->setEntityClassName($class_name);//by default
        $this->response = new Response();
        parent::__construct();
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
        foreach ($data as $key=>$value){
            if(property_exists($this, $key)){
                $this->{$key} = $value;
            }
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
                $this->removeUndefinedProperty();
                $data = ($this->toArray());
                $stmt = $this->queryBuilder->insert($this->table_name, $data)->execute();
                if($this->{$this->getKey()}=="" || $this->{$this->getKey()}==null){
                    $this->{$this->getKey()} = $this->queryBuilder::$conn->lastInsertId();
                }
                $this->response->set([
                    "msg" => "Record created successfully.",
                    "status"=>true,
                    "status_code"=>200,
                    "data"=>$this
                ]);
            }catch(Exception $e){
                $this->response->set([
                    "msg" => "Sorry, an error occurs while saving the record. ".$e->getMessage(),
                    "status"=>false,
                    "status_code"=>500,
                    "error"=>$this->queryBuilder->getErrorInfo(),
                    "error_code" => $this->queryBuilder->getErrorCode()
                ]);
            }
        }
        return $this->response;
    }
    //to read record
    public function read($fields = array()): EasyQueryBuilder{
        $this->queryBuilder->setEntityClassName(get_class($this));
        if((is_array($fields) && empty($fields)) || (is_string($fields) && trim($fields)==="")){
            $fields = $this->getReadableFields();
        }
        return $this->queryBuilder->select($fields)->from($this->table_name);
    }
    //to update and save record
    //Method save will work for both insertion if record does not exist and 
    //updation if record already exist
    
    public function save():Response{
        $temp = $this->toArray();//current data to be saved will be lost and replaced
        //if entity/record already exists, so we are temporarily storing current data
        if(is_null($this->find($this->{$this->key}))){
            //go for adding/creating new record
            return $this->add();
        }
        //record found so set the new record
        $this->setEntityData($temp);
        unset($temp);
        return $this->update();
    }
    
    public function update(): Response{
        if(!$this->isValidEntity()) {
            $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);          
        }
        else{
            try{
                $this->removeUndefinedProperty();
                $data = ($this->toArray());
                $cond = [
                    //primary key attribute = value
                    $this->key => ['=',$this->{$this->key}]
                ];
                unset($data[$this->key]);//key will not be updated
                $stmt = $this->queryBuilder
                        ->update($this->table_name)
                        ->set($data)
                        ->where($cond)
                        ->execute();
                
                $this->response->set([
                        "msg" => "Record updated successfully.",
                        "status"=>true,
                        "status_code"=>200,
                        "data"=>$data
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
                        ->delete($this->table_name)
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
    public function validate(){
        return parent::validate();
    }
    
    
    /*** find an Entity ***/
    public function find($id){
        //If Entity is not valid
        if(!$this->isValidEntity()) {
            throw new Exception(get_class($this)." is not a valid entity class, please make sure "
                    . "that you have set table name and primary key attribute of this entity.",500);
        }
        $stmt = $this->queryBuilder->select($this->getReadableFields())->from($this->table_name)->where([
            $this->key => ['=',$id]
        ])->execute();
        if($stmt->rowCount()==0){
            return null;            
        }
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($res as $col_name=>$val){
            $this->{$col_name} = $val;
        }
        foreach($this->hiddenFields as $field){
            unset($this->{$field});
        }
        return $this;        
    }
    /********** END CRUD OPERATIONS *********/
    
    //find maximum value of a column, the column should be of integer data type preferrably
    public function findMaxColumnValue($column/*Column/Attribute name*/,$cond=array()){
        $stmt = $this->queryBuilder->select(" max(".$column.") as max_val")
                ->from($this->getTable())
                ->where($cond)
                ->execute();
        if($stmt->rowCount() == 0){
            return 0;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['max_val'] == NULL?0:(is_numeric($row['max_val'])?(int)$row['max_val']:$row['max_val']);
    }
    /*
     * Security Feature:
     * 
     * Each property of an entity which has been declared public corresponds to the attribute
     * of the database table. So, if an unknown new property which is not an attribute of the 
     * table has been set at runtime accidentally by mistake or intensionally, then there might
     * be an error while updating record or populalting a new record, because that property 
     * does not exist in the table. So such property has to be removed before adding a new 
     * record or updating an existing record. Romoving of such unwanted or undefined property 
     * of an entity class is done by the function removeUndefinedProperty(). 
     * 
     */
    private function removeUndefinedProperty(){
        $data = $this->toArray();
        foreach ($data as $key=>$value){
            if(!property_exists(get_class($this), $key)){
                //unsetting unwanted properties from the entity object
                unset($this->{$key});
            }
        }
    }
    
    /*
     * For security reason, restriction is made from accessing non-existing
     * properties of the class. With this feature, any non-existing property 
     * can not be set dynamically.
     */
    public function __get($name) {
        if(!property_exists($this, $name)){
            return null;
        }
        return $this->{$name};
    }
    public function __set($name, $value) {
        if(property_exists($this, $name)){
            $this->{$name} = $value;
        }
    }
    
    /*
     * Method to get only readable fields after excluding some hidden fields.
     * Hidden Fields: In some case, when we don't want to disclose data for some 
     * fields, we can set those attributes/fields/columns in the variable $hiddenFields
     * as an array.
     * 
     * Readable Fields: are those whose values are allowed for retrieving. 
     */
    public function getReadableFields(){
        $fields = array_keys($this->toArray());
        $readableFields = array_diff($fields, $this->hiddenFields);
        return $readableFields;
    }
    /*
     *
     * Method to get hidden fields     */
    public function getHiddenFields():array{
        return $this->hiddenFields;
    }
    /*
     * Method to empty hidden fields to show all fields when records are retrieved.
     */
    public function clearHiddenFields():void{
        $this->hiddenFields = [];
    }
}