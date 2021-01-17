<?php
namespace MyEasyPHP\Libs\Attributes;
use Attribute;
use MyEasyPHP\Libs\EasyEntity as Entity;
/**
 * Description of Hidden
 * To set data member hidden from displaying in the public domain
 * 
 * This attribute class will only be working for EasyEntity class & Objects.
 * A property of an Entity object with attribute declared as [Hidden] will not be
 * displaying. For example password, security stamps etc will be kept hidden. 
 * @author Nganthoiba
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Hidden {
    public function hide(Entity $entity, string $property){
        $entity->addHiddenField($property);
    }
}
