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
    protected $file;
    protected $line;
    public int $httpCode;
    public function __construct(string $message = "",  $code = 0, \Throwable $previous = NULL) {
        if(is_integer($code)){
            parent::__construct($message, $code, $previous);
        }
        $this->details="";
        $this->httpCode = $code==0?200:500;
    }
    public function setLine($lineNo){
        $this->line = $lineNo;
        return $this;
    }
    public function setFile($file){
        $this->file = $file;
        return $this;
    }
    public function setDetails(string $details){
        $this->details = $details;
        return $this;
    }
    public function getDetails() : string{
        return "##**".$this->details;
    }
    
    public function getErrorLine(){
        return $this->line;
    }
    
    public function getErrorFile(){
        return $this->file;
    }
}
