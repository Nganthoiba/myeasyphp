<?php
declare(strict_types=1);
/*
 * ApiController class is the based class for RESTful api class
 */

namespace MyEasyPHP\Libs;

/**
 * Description of ApiController
 * This is the controller for RESTful api
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Router;
use MyEasyPHP\Libs\Request;
use MyEasyPHP\Libs\Response;
use MyEasyPHP\Libs\HttpStatus;

class ApiController {
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
    
    
    protected $dataModel;//either an entity or simply a model 
    
    /*$response is an object of Response class, a basic structure of how data will be responded. */
    public $response;
    
    public $request;
    
    /* $router is the one which parses the uri, and obtain the controller, action and perameters from the uri.*/
    private $router;
    
    protected $entityManager;

    public function setRouter(Router $router){
        $this->router = $router;
    }
    
    //these are the parameters appended in urls
    public function getParams(){
        return $this->params;
    }
    //these are the parameters appended in urls
    public function setParams($params){
        $this->params = $params;
    }
    //method to set request data
    public function setRequest(Request $request){
        $this->request = $request;
    }
    
    public function __construct() {
        $this->dataModel = null;
        $this->params = null;
        $this->response = new Response();
    }
    
    /** For sending response to client **/
    public function sendResponse(Response $resp){
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $resp->status_code . " " . $this->_requestStatus($resp->status_code));
        if(is_null($resp->msg) || $resp->msg == ""){
            $resp->msg = $this->_requestStatus($resp->status_code);
        }
        http_response_code($resp->status_code);
        return json_encode($resp);
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
        return HttpStatus::getStatus($code);
    }
    
    /*** functions corresponding to HTTP verbs for restful APIs ***/
    //such methods have to be overriden in the derived classes
    //method to read data
    protected function GET($id = null){
        //Override the body 
        $this->response->status_code = 200;
        $this->response->msg = "GET request, you can override method";
        return $this->sendResponse($this->response); 
    }
    
    //method to populate data
    protected function POST($data=null){
        //Override the body 
        $this->response->status_code = 201;
        $this->response->msg = "POST request, you can override method";
        return $this->sendResponse($this->response);
    }
    //method to update data
    protected function PUT($id = null){
        //Override the body 
        if(is_null($id)){
            $this->response->status_code = 403;
            $this->response->msg = "Missing parameter";
        }
        else{
            $this->response->status_code = 200;
            $this->response->msg = "PUT request, you can override method";
        }        
        return $this->sendResponse($this->response);
    }
    //method to delete data
    protected function DELETE($id = null){
        //Override the body 
        if(is_null($id)){
            $this->response->status_code = 403;
            $this->response->msg = "Missing parameter";
        }
        else{
            $this->response->status_code = 200;
            $this->response->msg = "DELETE request, you can override method";
        }        
        return $this->sendResponse($this->response);
    }
    
    public function index(){
        //this method can also be overridden
        $result = null;
        switch($this->request->getMethod()){
            case 'GET':
                $result = call_user_func_array([$this,"GET"], array_values($this->params));
                break;
            case 'PUT':
            case 'PATCH':
                $result = call_user_func_array([$this,"PUT"], array_values($this->params));
                break;
            case 'DELETE':
                $result = call_user_func_array([$this,"DELETE"], array_values($this->params));
                break;
            case 'POST':
                $result = $this->POST($this->request->getData());
                break;
        }
        return $result;       
        
    }
    
}
