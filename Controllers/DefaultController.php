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
    #[Route(url:'/about',methods:'GET')]
    public function about(){
        return view();
    }
    
    public function contact(ContactModel $model){
        if($this->request->isMethod("POST")){
            if($model->isValidated()){
                //return "Success"; 
            }
        }
        return view($model);
        //displayAndDie($model);
    }         
    public function home(){
        return view();
    }
}
