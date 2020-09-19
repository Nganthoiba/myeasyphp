<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Controllers;

/**
 * Description of EmailController
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\Controller;

use MyEasyPHP\Libs\ext\PHPMailer\PHPMailer;
use MyEasyPHP\Libs\ext\PHPMailer\SMTP;
use MyEasyPHP\Libs\ext\PHPMailer\Exception as MailerException;

class EmailController extends Controller{
    public function send(){
        // Instantiation and passing `true` enables exceptions
        
        $sender_email = "smartnotification1@gmail.com";                    
	$password = "smartnotice";
        try {
            $mail = new PHPMailer(true);
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $sender_email;                          // SMTP username
            $mail->Password   = $password;                              // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('smartnotification1@gmail.com', 'E-PASS');
            $mail->addAddress('leecba@gmail.com', 'Nganthoiba');     // Add a recipient
            //$mail->addAddress('meeraen.kl@gmail.com','Mathou');               // Name is optional
            //$mail->addReplyTo('smartnotification1@gmail.com', 'SMART NOTICE');
            
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Testing for covid';
            $mail->Body    = "This is a test of email check <b>Please don't mind!</b>";
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            
            if($mail->send()){
                return 'Message has been sent';
            }
        } 
        catch(MailerException $mailEx){
            return "Failed: ".$mailEx->getMessage();
        }
        catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    
    }
}
