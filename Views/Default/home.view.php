<?php 
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");

//highlight_file(CONTROLLERS_PATH.'DefaultController.php');
highlight_string(show_source(CONTROLLERS_PATH.'DefaultController.php', false),false);
?>