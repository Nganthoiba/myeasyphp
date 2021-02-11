<?php
use MyEasyPHP\Libs\Config;

$isAuthenticated = isAuthenticated();
$user_info = getLoginInfo();
?>
<nav class="navbar navbar-expand-lg navbar-dark custom-nav-grey">            
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
                    <a class="dropdown-item" href="<?= Config::get('host')?>/">
                        <span class="fa fa-home"></span>
                        Home
                    </a>
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
