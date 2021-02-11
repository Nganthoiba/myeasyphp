<?php
namespace MyEasyPHP\Models\Entities;
/**
 * Description of Students
 *
 * @author Nganthoiba
 */
use Doctrine\ORM\Mapping as ORM;
use MyEasyPHP\Libs\EasyEntity;
use MyEasyPHP\Libs\Attributes\Key;
/**
 * @ORM\Entity
 * @ORM\Table(name="Students",
 * uniqueConstraints={
 *    @ORM\UniqueConstraint(columns={"class","roll_number","school_name","section"})
 * })
 */
class Students extends EasyEntity{
    
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue **/
    #[Key]
    public $Student_id;
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
    public int $Roll_Number;
    
    /** @ORM\Column(type="string",length=255) **/
    public $School_Name;
    
    /** @ORM\Column(type="string",length=2) **/
    public $section;
    
    /** @ORM\Column(type="string",length=150) **/
    public $FatherName;
}
