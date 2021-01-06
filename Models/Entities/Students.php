<?php
namespace MyEasyPHP\Models\Entities;
/**
 * Description of Students
 *
 * @author Nganthoiba
 */
use Doctrine\ORM\Mapping as ORM;
use MyEasyPHP\Libs\EasyEntity;
/**
 * @ORM\Entity
 * @ORM\Table(name="Students",
 * uniqueConstraints={
 *    @ORM\UniqueConstraint(columns={"class","roll_number","school_name","section"})
 * })
 */
class Students extends EasyEntity{
    
    public function __construct() {
        parent::__construct();
        $this->setTable("Students");
        $this->setKey("student_id");
    }
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue **/
    public $student_id;
    /** 
     * @ORM\Column(type="string",length=100) 
     * **/
    public $student_name;
    /** 
     * @ORM\Column(type="string",length=5) 
     * **/
    public $class;
    /** 
     * @ORM\Column(type="integer") 
     * **/
    public $roll_number;
    /** @ORM\Column(type="string",length=255) **/
    public $school_name;
    /** @ORM\Column(type="string",length=2) **/
    public $section;
}
