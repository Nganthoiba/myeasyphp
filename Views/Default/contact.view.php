<?php 
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Models\ContactModel;
Config::set('default_view_container','_masterPage');

/** @var $model \MyEasyPHP\Models\ContactModel */
?>
<p>This is the contact form.</p>
<?php
echo "<pre>";
var_dump($model);
echo "</pre>";
?>

