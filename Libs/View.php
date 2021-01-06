<?php
declare(strict_types=1);

namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\ViewData;
use MyEasyPHP\Libs\MyEasyException;
use MyEasyPHP\Libs\Html;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of view
 *
 * @author Nganthoiba
 */
class View {
    protected $path; //view path
    protected $viewData; //view data
    protected $model;//Data Model, or an entity 
    
    protected static function getDefaultViewPath(){
        global $router;
        if(!$router){
            return false;
        }
        $controller = $router->getController();
        $template_name = $router->getAction().'.view.php';
        return VIEWS_PATH.$controller.DS.$template_name;
    }

    public function __construct($path = null, ViewData $viewData) {
        
        if(!$path || ($path==null) || trim($path)==""){
            $this->path = self::getDefaultViewPath();  
        }
        else{
            $this->path = $path;
        }
        if(!file_exists($this->path)){
            throw new MyEasyException("View file is not found in the path: ".$this->path,404);
        }
        $this->viewData = $viewData;
    }
    
    public function setDataModel($model){
        $this->model = $model;
        return $this;
    }
    
    //method for passing view data
    public function withViewData($data = array()){
        //$data must be an array of key and value pairs
        foreach($data as $key=>$val){
            $this->viewData->{$key} = $val;
        }
        return $this;
    }
    //Randering view data
    public function render(){
        ob_start();//turns on output buffering        
        Html::$View_Data = $viewData = $this->viewData; 
        Html::$Model_Object = $model = $this->model;
        $data = is_object($model)?json_decode(json_encode($model),true):$model;        
        if(is_array($data)){
            foreach ($data as $key=>$value){
                if(!is_numeric($key)){
                    //creating dynamic variables
                    ${$key} = $value;
                }
            }
        }
        if(file_exists($this->path)){
            include_once($this->path);
        }
        //Get current buffer contents and delete current output buffer.Returns the 
        //contents of the output buffer and end output buffering. 
        //If output buffering isn't active then FALSE is returned. 
        $content = ob_get_clean();
        return $content;
    }
    
    public function __toString() {
        return $this->render();
    }
}