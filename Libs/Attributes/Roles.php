<?php
namespace MyEasyPHP\Libs\Attributes;
use Attribute;
/**
 * Description of Roles
 * to set user roles for an action method
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Roles {
    public $roles;
    public function __construct() {
        $this->roles = func_get_args();
    }
}
