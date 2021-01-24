<?php
namespace MyEasyPHP\Libs\Attributes\Http\Verbs;

use Attribute;
/**
 * Description of HttpGet
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpPost extends HttpMethod{
    public function getMethod(): array{
        return ['POST'];
    }
}