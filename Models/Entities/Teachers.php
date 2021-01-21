<?php
namespace MyEasyPHP\Models\Entities;

/**
 * Description of Teachers
 *
 * @author Nganthoiba
 */
use Doctrine\ORM\Mapping as ORM;
use MyEasyPHP\Libs\EasyEntity as Entity;
use MyEasyPHP\Libs\Attributes\Display;
use MyEasyPHP\Libs\Attributes\Key;
use MyEasyPHP\Libs\Attributes\Validations\Required;
use MyEasyPHP\Libs\Attributes\Validations\PhoneNumber;

/**
 * @ORM\Entity
 * @ORM\Table(name="Teachers")
 */
class Teachers extends Entity{    
    /** @ORM\Id @ORM\Column(type="integer") **/
    #[Display(Name:'Teacher Number')]
    #[Required]
    #[Key]
    public $teacher_no;
    
    /** @ORM\Id @ORM\Column(type="string") **/
    #[Display(Name:'Class')]
    #[Required]
    #[Key]
    public $class;
    
    /**
     * @ORM\Column(type="string",length=140)
     */
    #[Display(Name:'Teacher Name')]
    #[Required]
    public $name;
    
    /**
     * @ORM\Column(type="string",length=140)
     */
    #[Display(Name:'Address')]
    public $address;
    
    /**
     * @ORM\Column(type="string",length=10)
     */
    #[Display(Name:'Contact Number')]
    #[Required]
    #[PhoneNumber]
    public $contact_no;
    
    /**
     * @ORM\Column(type="string",length=100)
     */
    #[Display(Name:'Specialized Subject')]
    #[Required]
    public $subject;
    
}
