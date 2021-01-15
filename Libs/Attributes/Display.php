<?php
namespace MyEasyPHP\Libs\Attributes;
use Attribute;
/**
 * Description of Display
 *
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Display {
    public string $Name;
    public function __construct(string $Name) {
        $this->Name = $Name;
    }
}
