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
use MyEasyPHP\Libs\Attributes\Key;
use ReflectionClass;
use ReflectionProperty;

class EasyEntity extends Model{
    protected $table_name;//name of the table in the database
    protected $keys = [];//it can be one primary key or compound primary keys, i.e. two or more 
    //attributes together forming  primary key
    protected $queryBuilder;
    protected $response;
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
    protected $dbConnectionName = "Default";/*Database connection name*/
    /* You can change to another database in the derived entity class. */
    
    public function __construct() {
        parent::__construct();
        /*By convention, an entity class name should be same as the table name 
         * that exists in database, otherwise you have to override the constructor and 
         * use $this->setTable() method to set table name */
        $class_name = get_class($this);//class name contains namespaces        
        $this->table_name = basename($class_name);//by default, the table name is set 
        //same as that of the entity class name
        $this->queryBuilder = new EasyQueryBuilder($this->dbConnectionName);
        $this->queryBuilder->setEntityClassName($class_name);//by default
        $this->response = new Response();
        
        //setting hidden fields if property found declared Hidden
        $this->setHiddenFields();
        //setting keys of the table
        $this->setKeyFields();
        
    }
    
    public function useConnection($dbConnectionName){
        try{
            $this->queryBuilder->useConnection($dbConnectionName);
        }
        catch(MyEasyPHP\Libs\MyEasyException $exception){
            $backtrace = debug_backtrace();
            $caller = array_shift($backtrace);
            
            //dd($caller);
            $exception->setErrorFile($caller['file']);
            $exception->setErrorLine($caller['line']);
            throw $exception;
        }
    }
    //method to set table name of the enity
    protected function setTable($table_name){
        $this->table_name = $table_name;
        return $this;
    }    
    //method to set key of the enity
    protected function setKey(){
        $this->keys = func_get_args();
        return $this;
    }
    
    public function addKey($key){
        if(!in_array($key, $this->keys)){
            $this->keys[] = $key;
        }
    }
    
    //method to get table name of an entity
    public function getTable(){
        return $this->table_name;
    }
    //method to get keys of the entity
    public function getKeys():array{
        return $this->keys;
    }
    
    //this method will be called only when entity has single key
    public function getKey(){
        return $this->keys[0]??"";
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
        if(trim($this->table_name) == "" || $this->table_name == "EasyEntity" || empty($this->keys) || trim($this->keys[0])===""){
            return false;//entity is invalid
        }
        return true;//entity is valid
    }
    
    /*** method to set data to an entity ***/
    public function setEntityData(array $data){
        parent::setModelData($data);
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
                $this->queryBuilder->insert($this->table_name, $data)->execute();
                
                if(\sizeof($this->getKeys()) === 1 && ($this->{$this->getKeys()[0]} === "" || is_null($this->{$this->getKeys()[0]}))){                    
                    $this->{$this->getKeys()[0]} = $this->queryBuilder::$conn->lastInsertId();                    
                    
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
        if(is_null($this->find($this->getKeyConditions()))){
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
            return $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]); 
        }
        try{
            $this->removeUndefinedProperty();
            $data = ($this->toArray());
            $cond = $this->getKeyConditions();
            foreach($this->keys as $key){
                unset($data[$key]);//key will not be updated
            }
            $this->queryBuilder->update($this->table_name)->set($data)->where($cond)->execute();
            $this->queryBuilder->clear();
            return $this->response->set([
                    "msg" => "Record updated successfully.",
                    "status"=>true,
                    "status_code"=>200,
                    "data"=>$data
                ]);            
        }catch(Exception $e){
            return $this->response->set([
                    "msg" => "Sorry, an error occurs while updating the record. ".$e->getMessage(),
                    "status"=>false,
                    "status_code"=>500,
                    "error"=>$this->queryBuilder->getErrorInfo()
                ]);
        }
    }
    
    //to delete record
    public function remove(): Response{
        if(!$this->isValidEntity()) {
            return $this->response->set([
                "msg" => "Invalid entity: either table name or key is not set.",
                "status"=>false,
                "status_code"=>400
            ]);            
        }
        try{
            $cond = $this->getKeyConditions();
            $stmt = $this->queryBuilder->delete($this->table_name)->where($cond)->execute();
            return $this->response->set([
                    "msg" => "Record removed successfully.",
                    "status"=>true,
                    "status_code"=>200
                ]);
        }catch(Exception $e){
            return $this->response->set([
                    "msg" => "Sorry, an error occurs while removing the record. ".$e->getMessage(),
                    "status"=>false,
                    "status_code"=>500,
                    "error"=>$this->queryBuilder->getErrorInfo()
                ]);
        }
    }
    
    /*** Entity Data Validation ***/
    //This method needs to be overridden in every extended entity class according to the purpose
    //otherwise this default validation method will be executed.
    public function isValidated():bool{
        return parent::isValidated();
    }
    
    
    /*** find an Entity ***/
    public function find($keyValuePairs = array()){
        //If Entity is not valid
        if(!$this->isValidEntity()) {
            throw new Exception(get_class($this)." is not a valid entity class, please make sure "
                    . "that you have set table name and primary key attribute of this entity.",500);
        }
        $cond = is_array($keyValuePairs)?$keyValuePairs:[$this->keys[0]=>['=',$keyValuePairs]];
        
        $stmt = $this->queryBuilder->select($this->getReadableFields())
                ->from($this->table_name)
                ->where($cond)->execute();
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
     * Each property of an entity which has been declared public corresponds to the attributes or fields
     * of the database table. So, if an unknown new property which is not an attribute of the 
     * table has been set at runtime accidentally by mistake or intensionally, then there might
     * be an error while updating record or populalting a new record, because that property 
     * does not exist in the table. So such property has to be removed before adding a new 
     * record or updating an existing record. Romoving of such unwanted or undefined property 
     * of an entity class is done by the function removeUndefinedProperty(). 
     * 
     */
    private function removeUndefinedProperty(){
        $array_keys = array_keys($this->toArray());
        foreach ($array_keys as $key){
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
        $fields = $this->getFields();
        foreach ($fields as $field){
            if(strtolower($field) === strtolower($name)){
                return $this->{$field};
            }
        }
        return null;
    }
    
    public function __set($name, $value) {        
        $fields = $this->getFields();
        foreach ($fields as $field){
            if(strtolower($field) === strtolower($name)){
                $this->{$field} = $value;
                break;
            }
        }
    }
    
    public function getFields():array{
        $fields = [];
        foreach ($this->reflectionProperties as $property){
            $fields[] = $property->getName();
        }
        return $fields;
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
        $fields = $this->getFields();
        $readableFields = array_diff($fields, $this->hiddenFields);
        return $readableFields;
    }
    /*
     *
     * Method to add hidden fields     */
    public function addHiddenField(string $field_name){
        if(trim($field_name) !== ""){
            $this->hiddenFields[]=$field_name;
        }
    }
    /*
     * Method to get hidden fields     */
    public function getHiddenFields():array{
        return $this->hiddenFields;
    }
    
    /*
     * Method to remove hidden fields     */
    public function removeHiddenField(array|string $field_name=[]){
        if(is_string($field_name)){
            unset($this->hiddenFields[$field_name]);
        }
        else{
            foreach ($field_name as $field){
                unset($this->hiddenFields[$field]);
            }
        }
    }
    /*
     * Method to empty hidden fields to show all fields when records are retrieved.
     */
    public function clearHiddenFields():void{
        $this->hiddenFields = [];
    }
    /*
     * This method will push those properties which have been declared [Hidden] into 
     * $hiddenFields variable. This method will be called from the constructor
     */
    protected function setHiddenFields(){        
        foreach($this->reflectionProperties as $property){
            foreach($property->getAttributes(Attributes\Hidden::class) as $attribute){
                $attribute->newInstance()->hide($this,$property->getName());                
            }
        }
    }
    /*
     * This method will push those properties which have been declared [Key] into 
     * $keys variable. This method will be called from the constructor
     */
    protected function setKeyFields(){        
        foreach($this->reflectionProperties as $property){
            foreach($property->getAttributes(Key::class) as $attribute){
                $this->addKey($property->getName());
            }
        }
    }
    
    //function to make conditions on primary keys for updating, deleting, This is useful when 
    //two or more attributes are combined to form primary key
    public function getKeyConditions():array{
        $cond = [];//conditions to be returned
        foreach($this->keys as $key){
            $cond[$key] = $this->{$key};
        }
        return $cond;
    }
}