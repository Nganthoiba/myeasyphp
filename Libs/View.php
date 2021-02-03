<?php
declare(strict_types=1);

namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\ViewData;
use MyEasyPHP\Libs\MyEasyException;
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\EmptyClass;
use MyEasyPHP\Libs\EasyEntity as Entity;
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\Routing\Route;
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
            $excp = new MyEasyException("Sorry, the page you are looking for is not found on this server.",404);        
            $excp->httpCode = HttpStatus::HTTP_NOT_FOUND;
            $excp->setDetails("View file {$this->path} is not found.");
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
    
    
    private function objectHtmlEntities($object){
        $tempObj = $object;        
        if($tempObj instanceof Model or $tempObj instanceof Entity){
            $m_arr = $this->toHtmlEntities(json_decode(json_encode($object),true));
            $tempObj->setModelData($m_arr);
        }
        return $tempObj;
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
                $data[$key] = $this->objectHtmlEntities($value);
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
            }
        }
        if(is_object($this->model)){
            $this->model = $this->objectHtmlEntities($this->model);
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
