<?php
namespace MyEasyPHP\Libs\Attributes\Http\Verbs;
use Attribute;
/**
 * Description of HttpMethod
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpMethod {
    protected $Methods;
    public function __construct(string|array $Methods=['GET']) { 
        $this->Methods = is_string($Methods)?preg_split("/[,|]/",$Methods):$Methods;
        //convert every methods in uppercase
        $limit = sizeof($this->Methods);
        for($i = 0; $i < $limit; $i++){            
            if(trim($this->Methods[$i]) === ""){
                //discarding blank spaces
                continue;
            }
            $this->Methods[$i] = strtoupper(trim($this->Methods[$i]));
        }
    }
    public function getMethod(){
        return $this->Methods;
    }
}
