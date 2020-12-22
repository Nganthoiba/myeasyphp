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
use MyEasyPHP\Models\Entities\Persons;
use MyEasyPHP\Libs\EasyQueryBuilder as QueryBuilder;
use MyEasyPHP\Libs\EasyEntityManager as EntityManager;
use MyEasyPHP\Libs\Database;
use PDOException;
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
    public function home(){
        return $this->view();
    }    
}
