<?php 
/*
 * This is the default web page where every view will be loaded by default. 
 * So it is simply termed as default view container meaning it contains or holds 
 * another view file.
 * 
 * Warning: Don't delete this file by mistake unless you set another view container, 
 * be careful.
 * 
 * if you don't want this file as view container or if you want to use another container to 
 * contain or hold your main view file then create a new view container file in the same
 * directory and use the following line in your view file.
 * Html::setContainer(<<path of your container file>>)
 * or directly set in the configuration
 * Config::set('default_view_container','<<path of your container file>>');
 * 
 */
?>
<!DOCTYPE HTML>
<html>
    <?= $viewData->content; ?>
</html>
