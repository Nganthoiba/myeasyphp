<?php
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Config;
Config::set('page_title','Signup first');
Config::set("default_view_container","Shared/_masterPage");
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
    body{        
        background-image: url("<?= Config::get('host') ?>/Assets/images/notebook.jpg");
        background-repeat: repeat-y;
        background-origin: content-box;
        background-size: cover;
    }
</style>
<div class="vertical-center">
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
                        <input type="text" class="form-control" id="UserName" name="UserName" 
                             value="<?= $viewData->RegisterModel->UserName ?>"
                             required/>
                        <label for="UserName">Full Name</label>
                    </div>
                    
                    <div class="md-form">
                        <i class="fa fa-envelope prefix"></i>
                        <input type="text" id="email" autocomplete="new-password"
                               name="Email" value="<?= $viewData->RegisterModel->Email ?>" class="form-control">
                        <label for="email">Your email</label>
                    </div>
                    
                    <div class="md-form">
                        <i class="fa fa-phone prefix"></i>
                        <input type="text" id="PhoneNumber" autocomplete="new-password"
                               name="PhoneNumber" value="<?= $viewData->RegisterModel->PhoneNumber ?>" class="form-control">
                        <label for="PhoneNumber">Your Contact Number</label>
                    </div>

                    <!-- Password -->
                    <div class="md-form">
                        <i class="fa fa-lock prefix"></i>
                        <input type="password" id="password" autocomplete="new-password"
                               name="Password" value="<?= $viewData->RegisterModel->Password ?>" class="form-control">
                        <label for="password">Password</label>
                    </div>
                    <!-- Confirm Password -->
                    <div class="md-form">
                        <i class="fa fa-lock prefix"></i>
                        <input type="password" id="Confirm_password" autocomplete="new-password"
                               name="Confirm_password" value="<?= $viewData->RegisterModel->Confirm_password ?>" class="form-control">
                        <label for="Confirm_password">Confirm Password</label>
                    </div>
                    <!-- Sign in button -->
                    <button class="btn btn-outline-primary btn-rounded btn-block my-4 waves-effect rounded-pill" type="submit">Submit</button>
                    
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