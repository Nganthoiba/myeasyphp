<?php
declare(strict_types=1);
/*
 * Customizing the exception
 */
namespace MyEasyPHP\Libs;

/**
 * Description of MyEasyException
 *
 * @author Nganthoiba
 */
use \Exception as Excp;
class MyEasyException extends Excp{
    private $details;
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = NULL) {
        parent::__construct($message, $code, $previous);
        $this->details="";
    }
    
    public function setDetails(string $details){
        $this->details = $details;
    }
    public function getDetails() : string{
        return "##**".$this->details;
    }
}