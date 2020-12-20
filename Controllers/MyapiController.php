<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Controllers;

/**
 * Description of MyapiController
 * Just for testing REST api
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\ApiController;
use MyEasyPHP\Models\Entities\Persons;
use MyEasyPHP\Libs\EasyEntityManager as EntityManager;
class MyapiController extends ApiController{
    private $em;
    public function __construct() {
        parent::__construct();
        $this->em = new EntityManager();
    }
    
    //Overriding
    protected function GET($id = null) {
        if(is_null($id)){
            $this->response->status = true;
            $this->response->data = $this->em->read(new Persons())->toList();
        }
        else{            
            $this->response->data = $this->em->find(new Persons(), $id);
            $this->response->status = is_null($this->response->data)?false:true;
            $this->response->status_code = is_null($this->response->data)?404:200;
        }
        //parent::GET($id);
        return $this->sendResponse($this->response);
    }
    
    //@Overriding
    protected function POST($data = null){
        if(is_null($data) || sizeof($data)==0){
            $this->response->status = 200;
            $this->response->msg = "Nothing to create, empty data";
        }
        else{
            $person = new Persons();
            $person->setEntityData($data);
            $this->response = $this->em->add($person);
        }
        return $this->sendResponse($this->response);
    }
    
    //@Override
    protected function PUT($id = null){
        if(is_null($id)){
            $this->response->status = false;
            $this->response->status_code = 400;
            $this->response->msg = "Missing parameter.";
        }
        else{
            $data = $this->request->getData();
            //$this->response->data = $data;
            
            $person = new Persons();
            $person->setEntityData($data);
            $person->id = $id;
            $this->response = $this->em->update($person);
            
        }
        return $this->sendResponse($this->response);
    }
    //@Override
    protected function DELETE($id = null) {
        if(is_null($id)){
            $this->response->status = false;
            $this->response->status_code = 403;
            $this->response->msg = "Missing parameter";
        }
        else{
            $person = $this->em->find(new Persons(),$id);
            if(is_null($person)){
                $this->response->status = false;
                $this->response->status_code = 404;
                $this->response->msg = "Person is not found";
            }
            else{
                $this->response = $this->em->remove($person);
            }
        }        
        return $this->sendResponse($this->response);
    }
    
}
