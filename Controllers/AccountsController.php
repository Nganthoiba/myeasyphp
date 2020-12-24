<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Controllers;

/**
 * Description of AccountsController
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Libs\Request;
use MyEasyPHP\Models\Entities\Users;
use MyEasyPHP\Models\RegisterModel;
use MyEasyPHP\Models\LoginModel;
use MyEasyPHP\Libs\EasyEntityManager as EntityManager;
use MyEasyPHP\Libs\UUID;
use MyEasyPHP\Models\LoginViewModel;
use MyEasyPHP\Models\Entities\PasswordResetLinks;
use MyEasyPHP\Libs\ext\PHPMailer\PHPMailer;
use MyEasyPHP\Libs\ext\PHPMailer\SMTP;
use MyEasyPHP\Libs\ext\PHPMailer\Exception;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Response;
use SimpleRoleProvider\RoleProvider;
class AccountsController extends Controller{
    private $em;//entity manager
    
    public function login(){
        if(LoginModel::isAuthenticated()){
            $this->redirect("Dashboard", "index");
        }
        $this->response->set([
            "status"=>true,
            "status_code"=>200,
            "msg"=>""
        ]);
        $this->viewData->Email = "";
        $this->viewData->Password = "";
        $this->viewData->response = $this->response;
        if($this->request->isMethod("POST")){
            $this->authenticate();
        }
        return view();
    }
    
    public function register(){
        if(LoginModel::isAuthenticated()){
            $this->redirect("Dashboard", "index");
        }
        $reg_model = new RegisterModel();
        $this->response->set([
            "status_code"=>200,
            "status"=>true,
            "msg"=>""
        ]);
        
        if($this->request->getMethod()=="POST"){
            //First check csrf token
            $this->response = verifyCSRFToken();
            if(!$this->response->status){
                $this->viewData->response = $this->response;
                $this->viewData->RegisterModel = $reg_model;
                return $this->view();
            }
            
            $data = $this->request->getData();
            $reg_model->setModelData($data);
            $this->submitRegistration($reg_model);
            if($this->response->status){
                $this->redirect("Default", "home");
            }
        }
        $this->viewData->response = $this->response;
        $this->viewData->RegisterModel = $reg_model;
        return $this->view();
    }
    
    private function authenticate(){
        
        $data = $this->request->getData();
        $loginViewModel = new LoginViewModel();
        $loginViewModel->setModelData($data);
        $this->viewData->Email = $loginViewModel->Email;
        $this->viewData->Password = $loginViewModel->Password;
        //verify csrf token
        $this->response = verifyCSRFToken();
        if(!$this->response->status){
            $this->viewData->response = $this->response;
            return;
        }
        
        $this->response = $loginViewModel->isValidModel();
        
        if($this->response->status){
            //if model is valid
            $this->em = new EntityManager();
            $user = $this->em->read(new Users())->where([
                "email"=>["=",$loginViewModel->Email]
            ])->getFirst();
            
            
            if(is_null($user)){
                $this->response->set([
                    "status"=>false,
                    "status_code"=>404,
                    "msg"=>"The email you have entered is not registered."
                ]);
                $this->viewData->response = $this->response;
                return;
            }
            
            $hashed_password = hash('sha256', $loginViewModel->Password.$user->security_stamp);//new hashed password
            //check if it matches existing hashed password
            if($hashed_password!=$user->user_password){
                $this->response->set([
                    "status"=>false,
                    "status_code"=>404,
                    "msg"=>"Wrong email or password."
                ]);
                $this->viewData->response = $this->response;
                return;
            }
            //Now everything is found correct and the user is authenticated saving a login details in session, then redirected
            //to the dashboard page
            $this->setLoginSession($user);
            $this->redirect("Default","home");
            
            /*
            $this->roleProvider = new RoleProvider();
            if ($this->roleProvider->IsUserInRole($user->full_name, "Admin")){
                //redirect to dashboard page
                $this->redirect("Dashboard","index");
            }
            else {
                $this->redirect("UserProfile","Index");
            }
            */
            
        }
        
        $this->viewData->response = $this->response;
    }
    
    private function submitRegistration(RegisterModel $reg_model){
        $this->em = new EntityManager();
        $this->response = $reg_model->isValidModel();
        if($this->response->status){        
            $user = new Users();
            $user->user_id = UUID::v4();
            $user->email = $reg_model->Email;            
            $user->create_at = date('Y-m-d H:i:s');
            $user->full_name = $reg_model->UserName;
            $user->phone_number = $reg_model->PhoneNumber;
            $user->security_stamp = UUID::v4();            
            $user->user_password = hash('sha256', $reg_model->Password.$user->security_stamp);
            $user->role_id=1;
            
            if($this->isEmailExist($user->email)){
                $this->response->set([
                    "status"=>false,
                    "msg"=>"Email already exist. Try another",
                    "status_code"=>403
                ]);
            }
            else{
                //inserting a new record in the users table
                $this->response = $this->em->add($user);
                if($this->response->status){
                    $this->setLoginSession($user);
                }
            }
        }
        $this->response->data = $reg_model;
    }

    private function isUserNameValid($username){
        // Minimum 6 characters, no space
        if(preg_match("/^[0-9a-zA-Z_]{6,}$/", $username) === 0)
            return false;
        return true;
    }
    
    private function isUserNameExist($username){
        $this->em = new EntityManager();
        $list = $this->em->read(new Users())->where([
            "full_name"=>$username
        ])->get();
        
        return (!is_null($list) && sizeof($list)>0);
    }
    
    private function isEmailExist($email){
        $this->em = new EntityManager();
        $list = $this->em->read(new Users())->where([
            "email"=>$email
        ])->get();
        
        return (!is_null($list) && sizeof($list)>0);
    }
    
    private function setLoginSession(Users $user){
        startSecureSession();
        $loginModel = new LoginModel($user);
        $loginModel->setLoginInfo();
    }
    
    public function logout(){
        LoginModel::destroyLoginInfo();
        $this->viewData->msg = "You have successfully logged out";
        return $this->view();
    }
    
    public function forgotPassword(){
        return $this->view();
    }
    public function generatePasswordResetCode(){
        $data = $this->request->getData();
        $this->viewData->ErrorMessage="";
        if(!isset($data['email'])){
            $this->response->set([
                "status"=>false,
                "msg"=>"Please submit your email."
            ]);
        }
        else if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            $this->response->set([
                "status"=>false,
                "msg"=>"Please submit a valid email."
            ]);
        }
        else if(!$this->isEmailExist($data['email'])){
            $this->response->set([
                "status"=>false,
                "msg"=>"The email you have entered is not registered."
            ]);
        }
        else{
            //now we can generate password reset link
            $email = $data['email'];
            $passReset = new PasswordResetLinks();
            //find user id for the email
            $this->em = new EntityManager();
            $user = $this->em->read(new Users())->where(["email"=>$email])->getFirst();
            
            $passReset->generate($user->Id);
            try{
                $this->response = $this->em->save($passReset);
                if($this->response->status){
                    $messageBody = "This is your password reset link, please click on the link or copy the link"
                            . " and paste into your browser:  <br/>".
                            "<a href='".$this->request->getHost()."/Accounts/resetPassword/".$passReset->ResetCode."'>"
                            ." ".$this->request->getHost()."/Accounts/resetPassword/".$passReset->ResetCode
                            . "</a>";
                    $this->response = $this->sendEmail($messageBody,$user);
                    
                    $this->response->msg = ($this->response->status)?"Password reset link has been generated"
                            . " and sent to your registered email, please check.":$this->response->msg;
                    
                }
            }
            catch(Exception $e){
                $this->response->set([
                    "status"=>false,
                    "status_code"=>500,
                    "msg"=>"Oops! Something went wrong. Sorry...",
                    "error"=>$e->getMessage()
                ]);
            }
        }
        $this->viewData->response = $this->response;
        return $this->view();
    }
    public function resetPassword(string $resetCode="null"){
        $this->em = new EntityManager();
        //first check if password reset code is valid
        $resetLink = $this->em->find(new PasswordResetLinks(),$resetCode);
        //echo json_encode($resetLink);
        if(is_null($resetLink)){
            $this->response->set([
                "msg" => "You password reset link is invalid",
                "status_code"=>401,//unauthorized request
                "status"=>false
            ]);
        }
        else{
            $createdAt = $resetLink->CreatedAt;
            $now = date("Y-m-d H:i:s");
            
            $timestamp1 = strtotime($createdAt);
            $timestamp2 = strtotime($now);
            $timeDiffHrs = abs($timestamp2 - $timestamp1)/(60*60);
            if($timeDiffHrs > 24){
                $this->response->set([
                    "msg" => "You password reset link is expired.",
                    "status_code"=>401,//unauthorized request
                    "status"=>false
                ]);
            }
            else{
                $this->response->set([
                    "msg" => "You password reset link is found valid.",
                    "status_code"=>200,//unauthorized request
                    "status"=>true
                ]);
                $_SESSION['reset_confirm_code'] = randId(10);
                
                $obj = new \MyEasyPHP\Libs\Model();
                $obj->UserId = $resetLink->UserId;
                $obj->reset_confirm_code = $_SESSION['reset_confirm_code'];
                $obj->resetCode = $resetCode;
                $this->viewData->response = $this->response;
                return $this->view($obj);
            }
        }
        $this->viewData->response = $this->response;
        return $this->view();
    }
    
    public function confirmPasswordReset(){
        if($this->request->isMethod("POST")){
            $data = $this->request->getData();
            //validation starts
            if(!isset($data['password']) || trim($data['password']) == ""){
                $this->response->set([
                        "status"=>false,
                        "msg" => "Missing password",
                        "status_code"=>403
                    ]);
            }
            else if(!isset($data['conf_password']) || trim($data['conf_password'])==""){
                $this->response->set([
                        "status"=>false,
                        "msg" => "Missing confirmation password",
                        "status_code"=>403
                    ]);
            }
            else if(!isset($data['UserId']) || !isset($data['reset_confirm_code']) || !isset($data['resetCode']) || trim($data['resetCode'])==""){
                $this->response->set([
                        "status"=>false,
                        "msg" => "Invalid attempt",
                        "status_code"=>403
                    ]);
            }
            else if($data['password']!==$data['conf_password']){
                $this->response->set([
                        "status"=>false,
                        "msg" => "Password does not match confirm password.",
                        "status_code"=>403
                    ]);
            }
            else if($data['reset_confirm_code']!==$_SESSION['reset_confirm_code']){
                $this->response->set([
                        "status"=>false,
                        "msg" => "Invalid attempt",
                        "status_code"=>403
                    ]);
            }
            else{
                $this->em = new EntityManager();
                $userId = $data['UserId'];
                $resetCode = $data['resetCode'];
                
                $resetLink = $this->em->read(new PasswordResetLinks())->where([
                    "UserId" => $userId,
                    "ResetCode" =>$resetCode
                ])->getFirst();
                
                $user = $this->em->find(new Users(), $userId);
                
              
                if(is_null($user) || is_null($resetLink)){
                    $this->response->set([
                        "status"=>false,
                        "msg" => "Invalid attempt to change password",
                        "status_code"=>403
                    ]);
                }
                else{
                    $this->em->beginTransaction();
                    $res = $this->em->remove($resetLink);
                    $user->security_stamp = \MyEasyPHP\Libs\UUID::v4();
                    $new_password = hash('sha256',$data['password'].$user->security_stamp);
                    $user->PasswordHash = $new_password;
                    $this->response = $this->em->update($user);
                    
                    if($this->response->status){
                        $this->em->commitTransaction();
                        $this->response->msg = "Password changed successfully";
                    }
                    else{
                        $this->response = $res;
                        $this->em->rollbackTransaction();
                        $this->response->msg = "Oops! Failed to change password, something went wrong";                               
                    }
                    
                }
            }
        }
         
        $this->viewData->response = $this->response;
        return $this->view();
    }
    //function to send email
    private function sendEmail($message,Users $recipent/*recipent email*/):Response{
        $resp = new \MyEasyPHP\Libs\Response();
        $email_config = Config::get('email_config');
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $sender_email = $email_config['Email'];                    
	$password = $email_config['Password'];
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = $email_config['Host'];                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $sender_email;                          // SMTP username
            $mail->Password   = $password;                              // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = $email_config['Port'];                                // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($sender_email, 'PPTTC');
            $mail->addAddress($recipent->Email, $recipent->UserName);     // Add a recipient
            
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Password Reset Link';
            $mail->Body    = "<b>Dear ".$recipent->UserName."</b> ".$message;
            $mail->AltBody = 'Dear '.$recipent->UserName.' '.$message;
            
            if($mail->send()){
                $resp->set([
                    "status"=>true,
                    "status_code"=>200,
                    "msg"=>'Message has been sent'
                ]);
            }
            else{
                $resp->set([
                    "status"=>false,
                    "status_code"=>500,
                    "msg"=>'Message has not been sent'
                ]);
            }
        } catch (Exception $e) {
            $resp->set([
                    "status"=>false,
                    "status_code"=>500,
                    "msg"=>'Message could not be sent.',
                    "error"=>[
                        "exception"=>$e,
                        "MailError"=>$mail->ErrorInfo
                    ]
                ]);
        }
        return $resp;
    }
}
