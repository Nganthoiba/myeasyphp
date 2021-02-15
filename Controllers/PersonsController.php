<?php
declare(strict_types=1);
/*
 * An example of how to use an API
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
class PersonsController extends ApiController{
    private $em;
    public function __construct() {
        parent::__construct();//important
        $this->em = new EntityManager('Postgres');
    }    
    
    //Overriding
    protected function GET($id = null) {
        if(is_null($id)){
            $this->response->status = true;
            $this->response->status_code = 200;
            $this->response->data = $this->em->read(new Persons())->toList();
        }
        else{            
            $this->response->data = $this->em->find(new Persons(), $id);
            $this->response->status = is_null($this->response->data)?false:true;
            $this->response->status_code = is_null($this->response->data)?404:200;
            $this->response->msg = is_null($this->response->data)?"Person is not found.":"";
        }
        //parent::GET($id);
        return $this->response->toJSON();
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
        return $this->response->toJSON();
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
            $person = $this->em->find(new Persons(),$id);
            if(is_null($person)){
                $this->response->set([
                    "status"=>false,
                    "status_code"=>404,
                    "msg"=>"Person not found."
                ]);
                return $this->response->toJSON();
            }
            $person->setEntityData($data);
            $person->id = $id;
            $this->response = $this->em->save($person);            
        }
        return $this->response->toJSON();
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
        return $this->response->toJSON();
    }
    
}
