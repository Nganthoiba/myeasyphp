<?php
namespace MyEasyPHP\Libs\Attributes\Http\Verbs;
use Attribute;
/**
 * Description of HttpGet
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpGet extends HttpMethod{
    public function getMethod(): array{
        return ['GET'];
    }
}
