<?php
/**
 * Description of DataTable
 * Server side ajax response for DataTable 
 * @author Nganthoiba
 */
namespace MyEasyPHP\Libs\ext;

use MyEasyPHP\Libs\Database;
use Exception;
class DataTable {
    private $draw,$row,$length,$orderByColumnIndex,$orderByColumnName,$globalSearchValue;
    private $SearchQuery, $SearchValues;
    private $conn;
    private $query;
    private $condition;
    private $db_server;
    private $columns;
    public function __construct() {
        $this->conn = Database::connect();
        $this->db_server = Database::$db_server;
        
        $request = new Request();
        $post = $request->getData();
        //getting all necessary requrements
        ## Read value
        $this->draw = $post['draw'];
        $this->start = $post['start'];
        $this->length = $post['length']; //No of rows display per page
        $this->columns = $post['columns'];//Getting all fields/columns for which the values need to be displayed
        
        $this->orderByColumnIndex = $post['order'][0]['column']; // Column index
        $this->orderByColumnName =  $this->columns[$this->orderByColumnIndex]['data']; // Column name
        $this->columnSortOrder = $post['order'][0]['dir']; // asc or desc
        $this->globalSearchValue = $post['search']['value']; // Global Search value
        
        $this->SearchQuery = "";
        $this->SearchValues = [];
        
        $this->query = "";
        $this->condition = "";
    }
    /** in case of complex SQL queries **/
    public function setQuery($query){
        $this->query = $query;
        return $this;
    }
    
    public function select($fields = array()){
        $this->query = "SELECT ".$this->stringifyColumns($fields)." ";
        return $this;
    }
    
    public function from($table){
        $this->query .= " FROM ".$table." ";
        return $this;
    }
    
    public function where($cond = ""){
        $this->condition = $cond;
        $this->query .= " WHERE ".$this->condition." ";
        return $this;
    }
    
    private function filterBy(){
        //serchable fields only
        $columns = $this->getSearchableFields();
        $text = ($this->db_server == "pgsql")?"::text":"";
        if(is_array($columns) && sizeof($columns)>0){  
            //for global search
            if(trim($this->globalSearchValue) !== ""){
                $this->SearchQuery = trim($this->condition) === ""?" WHERE (":" AND (";          

                foreach ($columns as $column){
                    $field_name = $column['data'];
                    $field_value = $column['search']['value'];
                    if(trim($field_value) === ""){
                        $this->SearchQuery .= " UPPER(".$field_name."{$text}) LIKE :".$field_name." OR";
                        
                        $this->SearchValues[$field_name] = "%".strtoupper($this->globalSearchValue)."%";
                    }
                }
                $this->SearchQuery = rtrim($this->SearchQuery, "OR"). ")";   
            }
        
            //for every column search  
            $subFilterQry = "";
            $subFilterValues = array();
            foreach ($columns as $column){
                $field_name = $column['data'];
                $field_value = $column['search']['value'];
                if(trim($field_value) !== ""){
                    $subFilterQry .= " UPPER(".$field_name."{$text}) LIKE :".$field_name." AND";
                    
                    $subFilterValues[$field_name] = "%".strtoupper($field_value)."%";
                }
            }
            $subFilterQry = rtrim($subFilterQry, "AND");   
            
            if(trim($subFilterQry)!== ""){
                $this->SearchQuery = trim($this->SearchQuery) === ""?$this->condition==""?" WHERE (".$subFilterQry.")":" OR(".$subFilterQry.") ":$this->SearchQuery." OR (".$subFilterQry.")";
            }
            if(sizeof($subFilterValues)>0){
                $this->SearchValues = array_merge($this->SearchValues,$subFilterValues);
            }
            
        }
        return $this;
    }
    
    public function processData(){
        $this->filterBy();// filter data with those searchable fields
        
        ## Total number of records without filtering
        $st = $this->conn->prepare($this->query);
        $res = $st->execute();
        $totalRecords = $st->rowCount();
        
        ## Total number of records with filtering
        $st = $this->conn->prepare($this->query." ".$this->SearchQuery);
        $res = $st->execute();
        $totalRecordwithFilter = $st->rowCount();
        
        if($this->orderByColumnName === "sl_no"){
            // column sl_no may not exist in table
            $qry = $this->query." ".$this->SearchQuery." ".$this->limit();
        }
        else{
            $qry = $this->query." ".$this->SearchQuery.
                " ORDER BY ".$this->orderByColumnName." ".$this->columnSortOrder." ". 
                $this->limit();
        }
        
        $records = [];
        $error = "";
        
        $stmt = $this->conn->prepare($qry);
        try{        
            $res = $stmt->execute($this->SearchValues);
            if($res){
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else{
                $error = "Failed to execute query";
            }
        }
        catch(Exception $e){
            $error = $e->getMessage();
        }
        
        $sl_no = $this->start;
        for($i=0; $i<sizeof($records); $i++){
            $records[$i]['sl_no'] = ++$sl_no;
        }
        
        ## Response
        $response = array(
            "draw" => intval($this->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $records,
            "Query" => $qry,
            "SearchValues" => $this->SearchValues,
            "error" => $error
        );
        return ($response);
    }
    
    private function limit(){
        $limit = '';
        if (intval($this->length) !== -1) {
            switch($this->db_server){
                case 'pgsql':
                case 'sqlite':
                    $limit = "LIMIT ".intval($this->length)." OFFSET ".intval($this->start)."";
                    break;
                case 'mysql':
                    $limit = "LIMIT ".intval($this->start).", ".intval($this->length)."";
                    break;
                case 'sqlsrv':
                    $limit = "OFFSET ".intval($this->start)." ROWS FETCH NEXT ".intval($this->length)." ROWS ONLY";
                    break;
            }
            
        }
        return $limit;
    }
    private function stringifyColumns($arr = array()): string{
        if(is_string($arr)){
            return " ".$arr." ";
        }
        if(sizeof($arr) == 0){
            return " * ";
        }
        $str = "";
        foreach ($arr as $value) {
            $str .= $value.",";
        }
        return rtrim($str,',');
    }
    
    protected function getSearchableFields(){
        $columns = $this->columns;
        $searchableFields = [];
        
        foreach ($columns as $column){
            if(($column['searchable'])==="true"){
                $searchableFields[] = $column;
            } 
        }
        return $searchableFields;
    }
    
}