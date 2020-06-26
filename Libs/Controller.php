<?php
declare(strict_types=1);
/**
 * Based class of controller class
 *
 * @author Nganthoiba
 */
namespace MyEasyPHP\Libs;

use MyEasyPHP\Libs\Router;
use MyEasyPHP\Libs\Request;
use MyEasyPHP\Libs\Response;
use MyEasyPHP\Libs\Dispatcher;
//use MyEasyPHP\Libs\EasyEntityManager;
use MyEasyPHP\Libs\View;
use MyEasyPHP\Libs\ViewData;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Model;
use MyEasyPHP\Libs\EasyEntity;

class Controller {
    /*Parameters: 
        '$params'
     * The URI e.g. http://something.com/default/test/param1/param2/param3
     * is splitted into four parts:
     * domain: http://something.com/,
     * controller: 'default' is the name of a controller
     * action: 'test' is one of a method/action of the controller and
     * parameters: the remaining parts param1/param2/param3 are the parameters, but these parameters
     * are parsed in the form of array as [param1, param2, param3]
     *      */
    protected $params;
    
    /*viewData : an object of ViewData class, 
     * which will be passed over view files for displaying*/
    protected $viewData; 
    protected $dataModel;//either an entity or simply a model 
    
    /*$response is an object of Response class, a basic structure of how data will be responded. */
    public $response;
    
    public $request;
    
    /* $router is the one which parses the uri, and obtain the controller, action and perameters from the uri.*/
    private $router;
    
    protected $entityManager,$easyEntityManager;

    public function setRouter(Router $router){
        $this->router = $router;
    }
    public function getData(){
        return $this->viewData;
    }
    
    //these are the parameters appended in urls
    public function getParams(){
        return $this->params;
    }
    //method to set request data
    public function setRequest(Request $request){
        $this->request = $request;
    }
    
    public function __construct(ViewData $viewData = null) {
        $this->viewData = $viewData===null?new ViewData():$viewData;
        $this->dataModel = null;
        $this->params = Dispatcher::getRouter()->getParams();
        $this->response = new Response();
        // obtaining the Doctrine entity manager
        //$this->entityManager = DoctrineEntityManager::getEntityManager();
        // obtaining Easy Entity Manager
        //$this->easyEntityManager = new EasyEntityManager();
        
    }
    
    /** For sending response to client **/
    public function sendResponse(Response $resp){
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $resp->status_code . " " . $this->_requestStatus($resp->status_code));
        return json_encode($resp);
    }
    
    public function redirect($controller, $action){
        header("Location: ".Config::get('host')."/".$controller."/".$action);
    }
    
    protected function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(htmlspecialchars(strip_tags($data)));
        }
        return $clean_input;
    }

    protected function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            400 => 'Bad request',
            401 => 'Unauthorized request',
            402 => 'Payment required',
            403 => 'Forbidden',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            500 => 'Internal Server Error'
        );
        return ($status[$code])?$status[$code]:$status[500]; 
    }
    
    protected function view($model_or_viewpath = null): View{
        if($model_or_viewpath instanceof Model or $model_or_viewpath instanceof EasyEntity){
            $this->dataModel = $model_or_viewpath;
            $view_path="";
        }
        else if(is_null($model_or_viewpath) || !is_string($model_or_viewpath)){
            $view_path="";
        }
        else{
            $view_path = $model_or_viewpath;
        }
        if($view_path !== ""){
            $class_name = get_class($this);
            $parts_class_name = explode("\\",$class_name);
            $controller_name = $parts_class_name[sizeof($parts_class_name)-1];
            $controller_name = str_replace("Controller","",$controller_name);
            //all the view pages have file extension ".view.php" as a convension of this framework
            if(file_exists(VIEWS_PATH.$view_path.'.view.php')){
                $view_path = VIEWS_PATH.$view_path.'.view.php';
            }
            //If view file exists in the sharable folder
            else if(file_exists(VIEWS_PATH."Shared".DS.$view_path.'.view.php')){
                $view_path = VIEWS_PATH."Shared".DS.$view_path.'.view.php';
            }
            else{
                $view_path = VIEWS_PATH.$controller_name.DS.$view_path.'.view.php';   
            }
        }
        
        $view_obj = new View($view_path,$this->viewData);
        if(!is_null($this->dataModel)){
            $view_obj->setDataModel($this->dataModel);
        }
        
        $this->viewData->content = $view_obj->render();
        $layout = Config::get('default_view_layout');//$this->router->getRoute();
        $layout_path = VIEWS_PATH.$layout.'.view.php';
        $layout_view_obj = new View($layout_path,$this->viewData);
        return $layout_view_obj;
    }
    //For displaying error informations
    public function error(){
        //$this->viewData->detail = "This is an error page.";
        return $this->view('error');
    }
    
}
