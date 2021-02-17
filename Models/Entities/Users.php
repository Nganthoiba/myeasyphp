<?php
namespace MyEasyPHP\Models\Entities;
/**
 * Description of Users
 *
 * @author Nganthoiba
 */
use MyEasyPHP\Libs\EasyEntity as Entity;
use MyEasyPHP\Libs\UUID;
use MyEasyPHP\Libs\Attributes\Hidden;
use MyEasyPHP\Libs\Attributes\Key;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="Users")
 */
class Users extends Entity{
    /*** data structures for user***/
    /** @ORM\Id @ORM\Column(type="string",length=36) */
    #[Key]
    public $user_id;
    /** @ORM\Column(type="string",length=160) */
    public $full_name;  
    /** @ORM\Column(type="string",length=100) */        
    public  $email;
    /** @ORM\Column(type="string",length=10) */
    public $phone_number;
    /** @ORM\Column(type="integer") */
    public $role_id;    
    /** @ORM\Column(type="boolean", options={"default": false}) */    
    public bool $verify;//email verification     
    /** @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"}) */
    public $create_at;
    /** @ORM\Column(type="datetime", nullable=true) */
    public $update_at;
    /** @ORM\Column(type="datetime", nullable=true) */
    public $delete_at;
    /** @ORM\Column(type="string",length=160, nullable=true) */
    public $profile_image;
    /** @ORM\Column(type="string",length=36, nullable=true) */
    public $update_by;    
    /** @ORM\Column(type="string",length=255) */
    #[Hidden]
    public $user_password;
    /** @ORM\Column(type="string",length=255) */
    #[Hidden]
    public $security_stamp;//salt
    
    
    public function setEntityData(array $data) {
        parent::setEntityData($data);
        $this->user_id = (is_null($this->user_id) || trim($this->user_id)=="")?UUID::v4():$this->user_id;
        $this->create_at = date('Y-m-d H:i:s');
        $this->security_stamp = (is_null($this->security_stamp) || trim($this->security_stamp)=="")?UUID::v4():$this->security_stamp;  
        //The password is encrypted with security stam as below:
        $this->user_password = hash('sha256', $this->user_password.$this->security_stamp);
    }    
    public function setModelData(array $data) {
        $this->setEntityData($data);
    }
}
