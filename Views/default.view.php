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
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
               
    </head>
    <body>
        <?= $viewData->content; ?>
    </body>
    
</html>
