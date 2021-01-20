<?php 
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
/*
 * This is the default web page where every view will be loaded.
 * 
 * Warning: Don't delete this file by mistake, be careful.
 */
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= Config::get('site_name') ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <!--<meta http-equiv="X-UA-Compatible" content="IE=edge">-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        //Loading CSS and JS files using loadAssets()
        Html::loadCss([
            "bootstrap",
            "mdb",
            "style"
        ]);
        Html::loadAssets("font_awesome/css/all.css");
        Html::loadAssets("font_awesome/css/font-awesome.css");
        Html::loadJs([
            "jquery"
        ]);
        
        ?>
    </head>
    <body>
        <?php
        Html::include('Shared/mdbNavbar');
        ?>
        <div style="padding-top: 10px">
            <?= $viewData->content; ?>
        </div>
    
    <?php
        Html::loadJs([
            "bootstrap",
            "mdb"
        ]);
    ?>
    </body>
    
</html>
