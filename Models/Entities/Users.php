<?php
namespace MyEasyPHP\Models\Entities;

/**
 * Description of Users
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\EasyEntity as Entity;
use MyEasyPHP\Libs\UUID;
class Users extends Entity{
    /*** data structures for user***/
    public  $user_id , 
            $full_name ,     
            $email  ,        
            $phone_number  ,     
            $role_id,        
            $user_password, 
            $security_stamp,//salt
            $verify,//email verification         
            $create_at,     
            $update_at,      
            $delete_at,      
            $profile_image,  
            $aadhaar,        
            $update_by;
    protected $hiddenFields = [
        "user_password","security_stamp",'phone_number'
    ];
    public function __construct() {
        parent::__construct();
        $this->setKey("user_id");
    }
    
    public function setEntityData(array $data) {
        parent::setEntityData($data);
        $this->user_id = (is_null($this->user_id) || trim($this->user_id)=="")?UUID::v4():$this->user_id;
        $this->create_at = date('Y-m-d H:i:s');
        $this->security_stamp = (is_null($this->security_stamp) || trim($this->security_stamp)=="")?UUID::v4():$this->security_stamp;  
        //user password must be in plain text at the time of submission. The password is 
        //then encrypted with security stam as below:
        $this->user_password = hash('sha256', $this->user_password.$this->security_stamp);
    }    
    public function setModelData(array $data) {
        $this->setEntityData($data);
    }
}
