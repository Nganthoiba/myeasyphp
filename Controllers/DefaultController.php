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
use MyEasyPHP\Libs\Attributes\Route;

class DefaultController extends Controller{
    public function index(){
        $data = $this->request->getData();
        return $this->view($data);//returning view
    }
    #[Route(url:'/about/{test}')]
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
    
    public function sum(int $a,float $b){
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
        echo "Base class: ".basename(get_class($model));
        return "<br/>Test Model";
    }
    
    public function getParameters(array $args,int $a, int $num2){
        echo "<pre>";  
        print_r($args);
        //print_r($b);
        echo "</pre><br/> A = ",$a;
        echo "<br/> B = ",$num2;
        
    }
}
