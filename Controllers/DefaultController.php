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
use MyEasyPHP\Models\ContactModel;
class DefaultController extends Controller{
    public function index(){
        return $this->view();//returning view
    }
    //as an api end point
    public function about(string $test=null){
        $this->viewData->teststring = $test;
        return view();
    }
    public function contact(ContactModel $model){
        if($this->request->isMethod("POST")){
            if($model->validate()){
                return "Success"; 
            }
        }
        return view($model);
    }         
    public function home(){
        $this->viewData->test = "This is a text.";
        return view([
            "isAuthenticated" => LoginModel::isAuthenticated(),
            "user_info" => LoginModel::getLoginInfo()
        ]);
    }  
    
    public function sum($a=0, $b=6){
        return "Sum of $a + $b =".($a+$b);
    }
    
    public function testModel(int $a,MyModel $model,int $b){
        echo "<pre>";  
        print_r($model);
        echo "</pre><br/>";
        echo "<br/>Parameters: ";
        echo "<pre>";
        print_r($this->getParams());
        echo "</pre>";
        /**/
        echo "a = ".$a." and b = ".$b;
        
        return "<br/>Test Model";
    }
    
    public function getParameters(int $a,int $b, array $args){
        echo "<pre>";  
        print_r($args);
        //print_r($b);
        echo "</pre><br/> A = ",$a;
        echo "<br/> B = ",$b;
        
    }
}
