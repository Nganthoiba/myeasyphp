<?php
namespace MyEasyPHP\Models\Entities;

/**
 * Description of Teachers
 *
 * @author Nganthoiba
 */
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="Teachers")
 */
class Teachers {
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue **/
    public $id;
    
    /**
     * @ORM\Column(type="string",length=140)
     */
    public $name;
    /**
     * @ORM\Column(type="string",length=140)
     */
    public $address;
    /**
     * @ORM\Column(type="string",length=10)
     */
    public $contact_no;
    /**
     * @ORM\Column(type="string",length=100)
     */
    public $subject;
    
}
