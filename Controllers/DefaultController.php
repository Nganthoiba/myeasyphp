<?php
declare(strict_types=1);
namespace MyEasyPHP\Controllers;

/**
 * Description of DefaultController
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Models\LoginModel;
use MyEasyPHP\Models\MyModel;
class DefaultController extends Controller{
    public function index(){
        return $this->view();//returning view
    }
    //as an api end point
    public function about(){
        return $this->view();
    }
    public function contact(){
        $model  = new \MyEasyPHP\Models\ContactModel();
        /*
        $model->Name = "Nganthoiba";
        $model->Email = "leecba@gmail.com";
        $model->Body = "This is the body";
        */
        $model->setModelData($this->request->getData());
        /*
        if($model->validate()){
            return "Success"; 
        }
        */
        return $this->view($model);
    }         
    public function home(){
        $this->viewData->test = "This is a text.";
        return view([
            "isAuthenticated" => LoginModel::isAuthenticated(),
            "user_info" => LoginModel::getLoginInfo()
        ]);
    }  
    public function show(int $number, float $n2){
        return "You have passed ".($number*$n2);
    }
    
    public function addPerson(MyModel $person){
        return "Person received: ".json_encode($person);
    }
}
