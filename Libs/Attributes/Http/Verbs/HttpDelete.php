<?php
namespace MyEasyPHP\Libs\Attributes\Http\Verbs;
use Attribute;
/**
 * Description of HttpDelete
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpDelete extends HttpMethod{
    public function getMethod():array{
        return ["DELETE"];
    }
}
