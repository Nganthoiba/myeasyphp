<?php 
use MyEasyPHP\Libs\Database;

global $db;
$db = Database::connect();
function get_data_from_db($arg){
 global $db;
    $result = ['success'=>0,'msg'=>'Error! Internal Server Error. Please try again...'];
    $table = $arg['table'];
    if( $table=="" ){
        return $result;
    }
    
    if( isset($arg['select'])  && is_array($arg['select']) ){
        $arg['select'] = implode(' , ',$arg['select']);
    }
    $select = $arg['select']??' * ';
    
    //setting where from where plain
    $where = $arg['where']??'';
    
    //setting where from params array
    $params = $arg['params']??array(); 
    $params_where = array();
    $params_value = $arg['params_value']??array(); //params_value may be included for plain where
    $index = 0;
    foreach($params as $fieldname => $value){
        $names = 'param_'.($index++);
        $params_where[] = " $fieldname = :$names "; //creating field = value pair on array
        $params_value[$names] = $value;    //inserting params value on array
    }
    
    //merging where from where plain and array params
    if (count($params_where) > 0) { //params where exist then make it plain
            $params_where = " ( ".implode(" AND ", $params_where)." ) ";
    }
    $params_where = is_array($params_where)?"":$params_where; //if array means no params so ""
    
    if(trim($where) != "" ){ //if plain where exist
        $where = " where ".$where." ";
        if(trim($params_where) != ""){ //and params also exist
                $where = $where." and ".$params_where;
            }
    }
    elseif(trim($params_where) != ""){ //no plain where but if params still exist 
        $where = " where ".$params_where;
    }
    
    //setting order by
    $order_by = "";
    if( isset($arg['order_by'])  && is_array($arg['order_by']) ){//if array make it plain
        $order_by = implode(' , ',$arg['order_by']);
    }
    if($order_by != ""){ 
        $order_by = " order by ".$arg['order_by'];
    }
    
    
    
    
    //merging select table where order_by
    $qry = "select {$select} from {$table} {$where} {$order_by}";
    $stmt = $db->prepare($qry);
    $resp = $stmt->execute($params_value);
    if(!$resp){
            $result['msg'] = "Internal Server Error. ";
            $result['error'] = $stmt->errorInfo();
            $result['params'] = $params_value;
            $result['raw_sql'] = $qry;
            return $result;
    }
    $rows = $stmt->fetchall(PDO::FETCH_ASSOC);
    if(!$stmt->rowCount()){
            $result['msg'] = "No data found. ";
            return $result;
    }
    $result = ['success'=>1, 'msg'=>'success', 'data'=>$rows, 'raw_sql'=>$qry];
    return $result;
    
}


function get_option_data($table, $name, $value ){
    global $db;
    $result = ['success'=>0,'msg'=>'Error! Internal Server Error. Please try again...'];
    $table = trim($table);
    $value = trim($value);
    $name = trim($name);
    if($table=="" || $value=="" || $name==""){
        return $result;
    }
        
    $qry = "select $value as value, $name as name from $table ";
    $stmt = $db->prepare($qry);
    $resp = $stmt->execute();
    if(!$resp){
            $result['msg'] = "Internal Server Error. ";
            $result['error'] = $stmt->errorInfo();
            $result['sql'] = $stmt->debugDumpParams();
            return $result;
    }
    $rows = $stmt->fetchall(PDO::FETCH_ASSOC);
    if(!$stmt->rowCount()){
            $result['msg'] = "No data found. ";
            return $result;
    }
    $result = ['success'=>1, 'msg'=>'success', 'data'=>$rows];
    return $result;
}
function change_to_option($data, $val = "", $field=""){
	
    $options = "";
	if($val == "" || $field == ""){
		foreach($data as $item){
			$options .= "<option value='".$item['value']."' >".$item['name']."</option>";
		}
	}
	else{
		foreach($data as $item){
			$options .= "<option value='".$item[$val]."' >".$item[$field]."</option>";
		}
	}
    
    return $options;
}