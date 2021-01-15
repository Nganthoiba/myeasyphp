<?php
namespace MyEasyPHP\Libs\Attributes;
use Attribute;
/**
 * Description of Route
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Route {
    public string $url;
    /* $methods: represents http verbs */
    public string|array $methods;
    
    public function __construct(string $url, string|array $methods=['GET']){
        $this->url = '/'.ltrim($url,'/');
        $this->methods = $methods;
    }
}
