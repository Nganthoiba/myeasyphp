<?php 
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;

$isAuthenticated = isAuthenticated();
$user_info = getLoginInfo();
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
        <nav class="navbar navbar-expand-lg navbar-dark custom-nav-grey fixed-top">            
            <div class="container-fluid">

                <!-- Navbar brand -->
                <a class="navbar-brand" href="javascript:void(0);">
                    <?php 
                    if($isAuthenticated){
                    ?>
                        <span class="navbar-toggler-icon" id="sidebarCollapse"></span>
                    <?php 
                    } 
                    ?>
                    <?= Config::get('app_name') ?>
                </a>

            <?php 
                if($isAuthenticated){
            ?>
                <ul class="navbar-nav"  style="padding:1px">
                    <!-- Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="navbardrop" data-toggle="dropdown" style="text-align: right">
                            <span class="user_full_name">
                                <?= $user_info->UserName ?>
                            </span>
                            <span class="fa fa-angle-down"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?= Config::get('host')?>/Accounts/manageAccount">
                                <span class="fa fa-cog"></span>
                                Manage Profile
                            </a>
                            <a class="dropdown-item" href="<?= Config::get('host')?>/Accounts/changePassword">
                                <span class="fa fa-lock"></span>
                                Change Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= Config::get('host')?>/Accounts/logout">
                                <span class="fa fa-sign-out"></span>
                                Log out
                            </a>
                        </div>
                    </li>
                </ul>
            <?php
                }
                else{
            ?>
                <a id="navbar-static-login" class="btn btn-default btn-rounded btn-sm waves-effect waves-light" 
                   href="<?= Config::get('host')?>/Accounts/login">Log In
                </a>
            <?php
                } 
            ?>
            <!-- Links -->
            </div>
        </nav>
        <div style="padding-top: 65px">
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
