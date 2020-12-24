<?php
declare(strict_types=1);
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
        return view();
        //view() is a global function in the entire application. It can be accessed
        //from anywhere. The function $this->view() is only accessible from inside the 
        //method of a controller class. $this->view() can take upto 2 parameters whereas, 
        //global view() can take upto 3 parameters.
    }    
}
