<?php 
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Models\LoginModel;

$isAuthenticated = LoginModel::isAuthenticated();
$user_info = LoginModel::getLoginInfo();
/*
 * 
 * Warning: Don't delete this file by mistake, be careful.
 */
Html::loadCss('side_navbar');
Html::loadAssets(["scrollbar/jquery.mCustomScrollbar.css","scrollbar/jquery.mCustomScrollbar.js"]);
?>
<!-- Sidebar  -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h2>Sidebar</h2>
    </div>
    <ul class="list-unstyled" style="margin-bottom: 0px;padding-bottom: 0px">
        <li>
            <div class="text-center">
                <img class="rounded-circle" 
                     src="<?= Html::getImage('images/picture.jpg')?>" alt="Not found"/>                            
            </div>
            <div style="color:#000000;padding:0px 5px 0px 5px;">
                Welcome, <br/>
                <span style="color: #49a75f; font-weight: normal"><?= $user_info->UserName ?></span><br/>
                <span style="color: #491217; font-weight: bold; font-size: 10pt">
                    (Admin)
                </span>
                <div class="dropdown-divider"></div>
            </div>
        </li>
    </ul>
    <ul class="list-unstyled components" style="margin-top: 0px;padding-top: 0px">
       
    </ul>
</nav>
<script type="text/javascript">
    $(document).ready(function () {
        $("#sidebar").mCustomScrollbar({
            theme: "minimal"
        });
        $('#sidebarCollapse').on('click', function () {
            $('.sidebar').toggleClass('active');
            $('#content').toggleClass('active');

            $('.collapse.in').toggleClass('in');
            $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        });
    });
</script>


