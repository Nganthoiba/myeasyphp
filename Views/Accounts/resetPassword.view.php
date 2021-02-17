<?php
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Config;
Config::set('page_title','Forgot Password?');
Html::setContainer('_masterPage');
?>
<style type="text/css">
    label{
        font-weight:bold;
    }
    section{
        margin-top: 73px;
        padding: 30px 0;
    }
</style>
<main id="main">
    <section>
        <div class="container" data-aos="fade-down">
            <div class=" col-md-5 m-auto">
                <div class="text-center">
                    <?php 
                    if($viewData->response->status == false){
                    ?>
                    <i class="fa fa-4x fa-exclamation-triangle text-warning"></i>
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
        </div>
        <div class="container" data-aos="fade-up">
            <div class=" col-md-5 m-auto">
                
                <div class="text-center <?= $viewData->response->status?"alert alert-success":"alert alert-warning" ?>">
                    <?= $viewData->response->msg ?>
                </div>

                <?php 
                if($viewData->response->status){
                ?>
                <p>Enter the followings to reset your password:</p>
                <form action="<?= Config::get('host') ?>/Accounts/confirmPasswordReset" method="POST">
                    <input type="hidden" name="resetCode" value="<?= $model->resetCode ?>" />
                    <input type="hidden" name="UserId" value="<?= $model->UserId ?>" />
                    <input type="hidden" name="reset_confirm_code" value="<?= $model->reset_confirm_code ?>" />
                    <input class="form-control" type="password" name="password" autocomplete="new-password"
                           placeholder="Your new password" required />
                    <br/>
                    <input class="form-control" type="password" name="conf_password" 
                           placeholder="Confirm password" required />
                    <br/>
                    <button class="btn btn-primary btn-block">Submit</button>
                </form>
                <?php
                }
                ?>
            </div>
        </div>
    </section>
</main>





