<?php
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Config;
Config::set('page_title','Signup first');
Html::setContainer('_masterPage');
?>
<style type="text/css">
    section{
        margin-top: 73px;
        padding: 30px 0;
    }
</style>
<style type="text/css">
    .transparency{
        background: rgb(0, 0, 0); /* Fallback color */
        /*background: rgba(0, 0, 0, 0.5); */
        /* Black background with 0.5 opacity */
        background:rgba(230, 228, 228, 0.5);
    }
    .register_layout {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    /*
    body{        
        background-image: url("<?= Config::get('host') ?>/Assets/images/notebook.jpg");
        background-repeat: repeat-y;
        background-origin: content-box;
        background-size: cover;
    }
    */
</style>
<!-- vertical-center -->
<div class="">
    <div class="col-md-4 mb-4 register_layout">
        <!-- Material form login -->
        <div class="card transparency">
            <h5 class="card-header default-color white-text text-center py-4">Complete Your <strong>Registration</strong></h5>
            
            <!--Card content-->
            <div class="card-body px-lg-5 pt-0">
                <!-- Form -->
                <form style="color: #757575;" method="POST" autocomplete="off">
                    <!-- CSRF token -->
                    <?= writeCSRFToken() ?>
                    <!-- Email -->
                    <div class="md-form">
                        <i class="fa fa-user prefix"></i>                        
                        <?php 
                        Html::textField($model, 'UserName',['class'=>'form-control']);
                        Html::label($model, 'UserName');                                
                        ?>
                    </div>
                    
                    <div class="md-form">
                        <i class="fa fa-envelope prefix"></i>
                        <?php 
                        Html::textField($model, 'Email',['class'=>'form-control','autocomplete'=>'new-password']);
                        Html::label($model, 'Email');                                
                        ?>
                    </div>
                    
                    <div class="md-form">
                        <i class="fa fa-phone prefix"></i>
                        <?php 
                        Html::textField($model, 'PhoneNumber',['class'=>'form-control','autocomplete'=>'new-password']);
                        Html::label($model, 'PhoneNumber');                                
                        ?>
                    </div>

                    <!-- Password -->
                    <div class="md-form">
                        <i class="fa fa-lock prefix"></i>
                        <?php
                        Html::passwordField($model, 'Password',['class'=>'form-control','autocomplete'=>'new-password']);
                        Html::label($model, 'Password'); 
                        ?>
                    </div>
                    <!-- Confirm Password -->
                    <div class="md-form">
                        <i class="fa fa-lock prefix"></i>
                        <?php
                        Html::passwordField($model, 'ConfirmPassword',['class'=>'form-control','autocomplete'=>'new-password']);
                        Html::label($model, 'ConfirmPassword'); 
                        ?>
                    </div>
                    <!-- Sign in button -->
                    <button class="btn btn-outline-primary btn-rounded btn-block my-4 waves-effect rounded-pill" type="submit">Submit</button>
                    
                </form>
                <!-- Form -->
                <p class="mb-0" style="text-align:center;color: red">
                    <strong><?= $viewData->response->status==false?$viewData->response->msg:"" ?></strong>
                </p>
          </div>

        </div>
        <!-- Material form login -->
    </div>
</div>