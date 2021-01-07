<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");
?>
<div class="container">
    <p>Index page of Default Controller</p>
    Hello <?= htmlspecialchars_decode($name); ?>
</div>