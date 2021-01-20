<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer('_masterPage');
?>
<style type="text/css">
    .transparency{
        background: rgb(0, 0, 0); /* Fallback color */
        /*background: rgba(0, 0, 0, 0.5); */
        /* Black background with 0.5 opacity */
        background:rgba(230, 228, 228, 0.5);
    }
    .login_layout {
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
    <div class="col-md-4 mb-4 login_layout">
        <!-- Material form login -->
        <div class="card transparency">
            <h5 class="card-header default-color white-text text-center py-4">Welcome, <strong>Sign in</strong></h5>
            
            <!--Card content-->
            <div class="card-body px-lg-5 pt-0">
                <!-- Form -->
                <form style="color: #757575;" method="POST" autocomplete="off">
                    <!-- CSRF token -->
                    <?= writeCSRFToken() ?>
                    <!-- Email -->
                    <div class="md-form">
                        <i class="fa fa-envelope prefix"></i>
                        <?php Html::textField($model, 'Email',['class'=>'form-control','autocomplete'=>'false', 'required']); ?>
                        <?php Html::label($model, 'Email') ?>
                    </div>

                    <!-- Password -->
                    <div class="md-form">
                        <i class="fa fa-lock prefix"></i>
                        <?php 
                            Html::passwordField($model, 'Password',
                                [
                                    'class'=>'form-control',
                                    'autocomplete'=>'new-password',
                                    'required'
                                ]); 
                            Html::label($model, 'Password');
                        ?>                        
                    </div>

                    <div class="d-flex justify-content-around">
                        <div>
                            <!-- Remember me -->
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="materialLoginFormRemember">
                                <label class="form-check-label" for="materialLoginFormRemember">Remember me</label>
                            </div>
                        </div>
                        
                        <div>
                            <!-- Forgot password -->
                            <a href="<?= Config::get('host') ?>/accounts/forgotPassword">Forgot password?</a>
                        </div>
                    </div>
                    <!-- Sign in button -->
                    <button class="btn btn-outline-primary btn-rounded btn-block my-4 waves-effect rounded-pill" type="submit">Sign in</button>
                    <div class="text-center">
                        <!-- Register -->
                        <p>Not a member?
                            <a href="<?= Config::get('host') ?>/accounts/register">Sign Up</a>
                        </p>
                        <p style="color: red"></p>
                        <!-- Social login -->
                        <p>or sign in with:</p>

                        <a type="button" class="btn-floating btn-fb btn-sm">
                          <i class="fab fa-facebook-f"></i>
                        </a>
                        <a type="button" class="btn-floating btn-tw btn-sm">
                          <i class="fab fa-twitter"></i>
                        </a>
                        <a type="button" class="btn-floating btn-li btn-sm">
                          <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a type="button" class="btn-floating btn-git btn-sm">
                          <i class="fab fa-github"></i>
                        </a>
                    </div>
                </form>
                <!-- Form -->
                <p class="mb-0" style="text-align:center;color: red">
                    <strong><?= $viewData->response->msg ?></strong>
                </p>
          </div>

        </div>
        <!-- Material form login -->
    </div>
</div>


