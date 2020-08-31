<?php
declare(strict_types=1);
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
        return $this->view();//returning view
    }
    
    //as an api end point
    public function about(){
        return $this->view();
    }
    public function contact(){
        return "This is my contact";//returning a string
    }
    //en example of how to handle parameters
    public function hello($fname, $lname){
        return "Hello! ".$fname." ".$lname;
    }
    
    public function test($script){
        $this->viewData->script = $script;
        return $this->view();//->withViewData($args);
    }
    
    public function sum($num1,$num2){
        return "Num1=$num1 & Num2=$num2, and sum is ".($num1+$num2);
    }
    public function product(int $num1,int $num2){
        return "Num1=$num1 & Num2=$num2, and product is ".($num1*$num2);
    }
    
    public function cropImage(){
        return $this->view();
    }
    
    public function headers(){
        $headers = $this->request->getRequestHeaders();

        foreach ($headers as $header => $value) {
            echo "$header: $value <br />\n";
        }
    }
    
    public function home(){
        return $this->view();
    }
}
