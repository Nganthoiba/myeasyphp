<?php
declare(strict_types=1);
namespace MyEasyPHP\Controllers;

/**
 * Description of DefaultController
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Models\ContactModel;
use MyEasyPHP\Libs\Attributes\Route;
use MyEasyPHP\Libs\Attributes\Http\Verbs\HttpPost;
use MyEasyPHP\Libs\Attributes\Http\Verbs\HttpDelete;

class DefaultController extends Controller{
    #[HttpPost]
    #[HttpDelete]
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
