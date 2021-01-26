<?php
declare(strict_types=1);

namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\ViewData;
use MyEasyPHP\Libs\MyEasyException;
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\EmptyClass;
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
    protected object|array $model;//Data Model, or an entity, or just an array
    
    public function __construct($path, ViewData $viewData) {
        
        if(!$path || ($path==null) || trim($path)==""){
            $this->path = self::getDefaultViewPath();  
        }
        else{
            $this->path = $path;
        }
        if(!file_exists($this->path)){
            $excp = new MyEasyException("View file is not found in the path: ".$this->path,404);
        
            $excp->httpCode = 404;
            $excp->setFile('');
            $excp->setLine(-1);
            throw $excp;
        }
        $this->viewData = $viewData;
        $this->model = new EmptyClass();//by default
    }
    protected static function getDefaultViewPath(){
        global $router;
        if(!$router){
            return false;
        }
        $controller = ($router->getController());
        $template_name = $router->getAction().'.view.php';
        return VIEWS_PATH.$controller.DS.$template_name;
    }
    
    public function setDataModel($model){
        $this->model = $model;
        return $this;
    }
    
    //Convert all applicable characters to HTML entities recurssively, whether object
    //or array
    private function toHtmlEntities(?array $data=null):array{
        if(is_null($data)){
            return [];
        }        
        foreach ($data as $key=>$value){
            if(is_array($value)){
                $data[$key] = $this->toHtmlEntities($value);
            }
            else if(is_object($value)){
                $tempObj = $value;
                $m_arr = $this->toHtmlEntities(json_decode(json_encode($value),true));
                if($tempObj instanceof Model){
                    $tempObj->setModelData($m_arr);
                }
                else{
                    $tempObj = (object)$m_arr;
                }
                $data[$key] = $tempObj;//$this->toHtmlEntities();
            }
            else if(is_string($value)){
                $data[$key] = \htmlentities(''.$value);
            }
        }
        return $data;
    }
    //Randering view
    public function render(){
        ob_start();//turns on output buffering 
        $data = is_object($this->model)?json_decode(json_encode($this->model),true):$this->model;         
        if(is_array($data)){
            $data = $this->toHtmlEntities($data);
            foreach ($data as $key=>$value){
                if(!is_numeric($key)){
                    //creating dynamic variables
                    ${$key} = $value;
                }
                if(is_object($this->model)){
                    $this->model->{$key} = $value;
                }
            }
        }
        Html::$View_Data = $viewData = $this->viewData; 
        Html::$Model_Object = $model = $this->model;
        
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
