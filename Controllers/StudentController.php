<?php

namespace MyEasyPHP\Controllers;

/**
 * Description of StudentController
 *
 * @author Nganthoiba
 */

use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Libs\DoctrineEntityManager;
use MyEasyPHP\Models\Entities\Students;
class StudentController extends Controller{
    
    public function __construct(\MyEasyPHP\Libs\ViewData $viewData = null) {
        parent::__construct($viewData);
        $this->entityManager = DoctrineEntityManager::getEntityManager();
    }
    //action to read all student data
    public function index(){
        $students = $this->entityManager->getRepository('Students')->findAll();
        $this->viewData->response = $this->response->set([
            "data"=>$students,
            "msg"=>"Records",
            "status"=>true,
            "status_code"=>200
        ]);
        return $this->view();        
    }
    
    public function add(Students $student){
       
        $request = $this->request;
        if($request->isMethod("POST")){
            try{
                $this->entityManager->persist($student);
                $this->entityManager->flush();
                redirect("Student");
                
            } catch (Exception $e){
                $this->response->set([
                    "msg" => $e->getMessage(),
                    "status_code"=>500,
                    "status"=>false
                ]);
            }        
            $this->viewData->response = $this->response; 
        }
        return $this->view($student);
    }
    
    public function edit($id){        
        $student = $this->entityManager->find("Students",$id);
        
        if($student === null){
            $this->response->set([
                "status"=>false,
                "status_code"=>404,
                "msg"=>"No data found."
            ]);
            $student = new Students();
        }
        else{            
            if($this->request->isMethod("POST")){                
                try{                    
                    $student->setEntityData($this->request->getData());
                    $this->entityManager->persist($student);
                    $this->entityManager->flush();
                    redirect("Student");
                    
                } catch (Exception $e){
                    $this->response->set([
                        "msg" => $e->getMessage(),
                        "status_code"=>500,
                        "status"=>false
                    ]);
                }
            }
            else{
                $this->response->set([
                    "status"=>true,
                    "status_code"=>200,
                    "msg"=>"",
                    "data"=>$student
                ]);
            }
        }
        $this->viewData->response = $this->response;
        return $this->view($student);
    }
    
    public function delete(int $id){  
        $student = $this->entityManager->find("Students",$id);        
        
        if($student == null){
            $this->response->set([
                "status"=>false,
                "status_code"=>404,
                "msg"=>"No data found."
            ]);
            $this->viewData->response = $this->response;
            return $this->view();
        }
        
        try{
            $this->entityManager->remove($student);
            $this->entityManager->flush();
            $this->response->set([
                        "msg" => "Record deleted",
                        "status_code"=>200,
                        "status"=>true,
                        "data" => $student
                    ]);
        }
        catch(Exception $e){
            $this->response->set([
                "msg" => $e->getMessage(),
                "status_code"=>500,
                "status"=>false
            ]);
        }
        $this->viewData->response = $this->response;
        return $this->view();
    }
    
    public function confirmDelete(int $id){
        $student = $this->entityManager->find("Students",$id);
        
        if($student == null){
            $this->response->set([
                "status"=>false,
                "status_code"=>404,
                "msg"=>"No data found."
            ]);
            $this->viewData->response = $this->response;
            return $this->view();
        }
        $this->response->set([
                "status"=>true,
                "status_code"=>200,
                "msg"=>"",
                "data"=>$student
            ]);
        
        $this->viewData->response = $this->response;
        return $this->view();
    }
}
