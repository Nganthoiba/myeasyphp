<?php
declare(strict_types=1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Controllers;

/**
 * Description of DefaultController
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Models\Entities\Persons;
use MyEasyPHP\Libs\EasyQueryBuilder as QueryBuilder;
use MyEasyPHP\Libs\EasyEntityManager as EntityManager;
use MyEasyPHP\Libs\Database;
use PDO;
use PDOException;
class DefaultController extends Controller{
    public function index(){
        return $this->view();//returning view
    }
    
    //as an api end point
    public function about(){
        return $this->view();
    }
    public function contact(){
        return "This is my contact";//returning a string
    }
    //en example of how to handle parameters
    public function hello($fname, $lname){
        return "Hello! ".$fname." ".$lname;
    }
    
    public function test($script){
        $this->viewData->script = $script;
        return $this->view();//->withViewData($args);
    }
    
    public function sum($num1,$num2){
        return "Num1=$num1 & Num2=$num2, and sum is ".($num1+$num2);
    }
    public function product(int $num1,int $num2){
        return "Num1=$num1 & Num2=$num2, and product is ".($num1*$num2);
    }
    
    public function cropImage(){
        return $this->view();
    }
    
    public function headers(){
        $headers = $this->request->getRequestHeaders();

        foreach ($headers as $header => $value) {
            echo "$header: $value <br />\n";
        }
    }
    
    public function home(){
        return $this->view();
    }
    
    public function addPerson(){
        $i = 0;
        $this->entityManager = new EntityManager();
        $conn = $this->entityManager->getConnection();
        
        if(!$conn){
            die("Connection is not open");
        }
        
        try{            
            $this->entityManager->beginTransaction();          
            $qry = "insert into xy(x,y) values(28,'Nganthoiba')";
            $conn->query($qry);
            for($i=1; $i<=10; $i++){
                
                $person = new Persons();
                $person->person_name = "person".$i;
                $person->phone_no = $i;
                $person->address = "Address".$i;
                $this->entityManager->add($person);
                
                if($i == 5){
                    //$conn->rollback();
                    $this->entityManager->rollbackTransaction();
                    return ("Rollbacked");
                    break;
                }
            }
            //$conn->commit();
            $this->entityManager->commitTransaction();
            return ($i-1)." Record(s) saved";
        }
        catch(PDOException $e){
            //$conn->rollback();
            $this->entityManager->rollbackTransaction();
            return ($i-1)." Record(s) saved. p ".($e->getMessage());
        }
        catch(Exception $e){
            //$conn->rollback();
            $this->entityManager->rollbackTransaction();
            return ($i-1)." Record(s) saved. n".$e->getMessage();
        }
        
        
    }
    
    public function txn($id,$name){
        $pdo = Database::connect();
        

        //We will need to wrap our queries inside a TRY / CATCH block.
        //That way, we can rollback the transaction if a query fails and a PDO exception occurs.
        try{

            //We start our transaction.
            $pdo->beginTransaction();
            
            $qry = "insert into persons(person_name,phone_no,address)"
                        . "values(:person_name,:phone_no,:address)";
            $stmt = $pdo->prepare($qry);
            $stmt->execute([
                                "person_name"=>$name,
                                "phone_no" => 951267341,
                                "address" => "MEKOLA"
                            ]);
            
//            if($name == 'test'){
//                $pdo->rollBack();
//                return "Test entered.";
//            }
            
            //Query 2: Attempt to update the user's profile.
            $sql = "INSERT INTO xy(x,y) values(?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                    $id, 
                    $name
                )
            );

            //We've got this far without an exception, so commit the changes.
            $pdo->commit();

        } 
        //Our catch block will handle any exceptions that are thrown.
        catch(Exception $e){
            //An exception has occured, which means that one of our database queries
            //failed.            
            //Rollback the transaction.
            $pdo->rollBack();
            //Print out the error message.
            return $e->getMessage();
        }
        catch(PDOException $e){
            $pdo->rollBack();
            //Print out the error message.
            return $e->getMessage();
        }
        return "Record saved";
    }
}
