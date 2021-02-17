<?php
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Config;
Config::set('page_title','Forgot Password?');
Html::setContainer('_masterPage');
?>
<style type="text/css">
    section{
        margin-top: 73px;
        padding: 30px 0;
    }
</style>
<main id="main">
    <section>
        
        <div class="container" data-aos="fade-down">
            <div class="text-center">
                <?php 
                if($viewData->response->status == false){
                ?>
                <i class="fa fa-4x fa-exclamation-triangle text-danger"></i>
                <?php 
                }
                else{
                ?>
                <i class="fa fa-4x fa-check text-success"></i>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="container" data-aos="fade-up">
            <div class="alert <?= $viewData->response->status?"alert-success":"alert-danger" ?> text-center">
                <?= $viewData->response->msg ?>
            </div>
            <div class="text-center">
                <?php 
                if($viewData->response->status){
                ?>
                <a href="<?= Config::get('host') ?>/Accounts/login">Click to login</a>
                <?php
                } 
                ?>
            </div>            
        </div>
    </section>
</main>

