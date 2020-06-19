<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Controllers;

/**
 * Description of GentelellaController
 *
 * @author Nganthoiba
 * only for theme
 */
use MyEasyPHP\Libs\Controller;

class GentelellaController extends Controller{
    //dashboard 1
    public function index(){
        return $this->view();
    }
    public function dashboard2(){
        return $this->view();
    }
    public function dashboard3(){
        return $this->view();
    }
    public function plainPage(){
        return $this->view();
    }
    public function tablesDynamic(){
        return $this->view();
    }
    public function tables(){
        return $this->view();
    }
    //documentation on gentelella
    public function documentation(){
        return $this->view();
    }
    
    public function generalForm(){
        return $this->view();
    }
    public function login(){
        return $this->view();
    }
}
