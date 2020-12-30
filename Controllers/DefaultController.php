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
    
    public function sum(int $a, int $b){
        return "Sum is: ".($a+$b);
    }
    
    public function testModel(MyModel $model, int $a, int $b){
        echo "<pre>";  
        print_r($model);
        echo "</pre><br/>";
        
        echo "Sum of ($model->x + $model->y) is : ".$model->sum();
        echo "<br/>Parameters: ";
        echo "<pre>";
        print_r($this->getParams());
        echo "</pre>";
    }
}
