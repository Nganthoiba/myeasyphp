<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");

$name = htmlspecialchars_decode($name);
//header("Location: ".Config::get('host')."/".$name."/index");
//redirect($name);
?>
<div class="container">
    <p>Index page of Default Controller</p>
    Hello <?= $name ?>
</div>