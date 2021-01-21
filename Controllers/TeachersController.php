<?php
namespace MyEasyPHP\Controllers;
use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Models\Entities\Teachers as Teacher;
use MyEasyPHP\Libs\EasyEntityManager as EntityManager;
use MyEasyPHP\Libs\Attributes\Route;
class TeachersController extends Controller{
    
    //create (C)
    #[Route(url:'Teachers/create',methods:['GET','POST'])]
    public function create(Teacher $teacher){
        $this->viewData->response = $this->response;
        if($this->request->isPost()){
            if($teacher->isValidated()){
                $this->entityManager = new EntityManager();
                $this->viewData->response = $this->entityManager->add($teacher);
            }
        }
        return view($teacher);
    }
    //read (R)
    #[Route(url:'Teachers/read',methods:['GET'])]
    public function read(){
        $this->entityManager = new EntityManager();
        $list = $this->entityManager->read(new Teacher())->orderBy('teacher_no')->toList();
        return view(['teachers'=>$list]);
    }
    //update (U)
    #[Route(url:'Teachers/update/{teacher_no}/{class}',methods:['GET','POST'])]
    public function update(int $teacher_no,string $class){
        $this->viewData->response = $this->response;
        $this->entityManager = new EntityManager();
        $teacher = $this->entityManager->find(new Teacher(),[
            'teacher_no' => $teacher_no,
            'class' => $class
        ]);
        
        if($this->request->isPost()){
            $teacher->setEntityData($this->request->getData());
            $teacher->teacher_no = $teacher_no;
            $teacher->class = $class;
            $this->viewData->response = $this->entityManager->save($teacher);
        }
        
        return view($teacher);
    }
    
    //delete (D)
    #[Route(url:'Teachers/delete/{teacher_no}/{class}')]
    public function delete(int $teacher_no,string $class){
        $this->viewData->response = $this->response;
        $this->entityManager = new EntityManager();
        $teacher = $this->entityManager->find(new Teacher(),[
            'teacher_no' => $teacher_no,
            'class' => $class
        ]);
        if(is_null($teacher)){
            http_response_code(404);
            $this->viewData->response->msg = "Resource not found.";
        }
        else{
            $this->viewData->response = $this->entityManager->remove($teacher);
        }
        return view();
    }    
}
