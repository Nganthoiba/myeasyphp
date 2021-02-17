<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer('_masterPage');
$class = $viewData->response->status==true?"alert-success":"alert-danger";
?>
<style>
    label{
        font-weight:bold;
    }
</style>
<div class="col-sm-6" style="margin:auto">
    <div class="text-center">
        <h2>Password Forgotten?</h2>
    </div>
    <div class="text-center alert <?= $class ?>">
        <?= $viewData->response->msg ?>   
    </div>  
    <div class="text-center">
        <?php
        if($viewData->response->status == false){
        ?>
        <a href="<?= Config::get('host') ?>/Accounts/forgotPassword">Back</a>
        <?php
        }
        ?>
    </div>
</div>

