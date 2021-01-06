<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");
?>
<link href="<?= Config::get('css') ?>/carousel.css" rel="stylesheet" type="text/css"/>
<div class="jumbotron">
    <div class="container">
        <h1><?= $viewData->response->msg ?></h1>
    </div>
</div>

