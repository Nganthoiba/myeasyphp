<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Controllers;

/**
 * Description of DefaultController
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Controller;
class DefaultController extends Controller{
    public function index(){
        return $this->view();
    }
    public function about(){
        $this->response->set([
            "status"=>true,
            "status_code"=>200,
            "msg"=>"Wow! wonderful"
        ]);
        return $this->sendResponse($this->response);
    }
    public function contact(){
        return "This is my contact";
    }
    
    public function hello($params){
        return "Hello! ".$params['fname']." ".$params['lname'];
    }
}
