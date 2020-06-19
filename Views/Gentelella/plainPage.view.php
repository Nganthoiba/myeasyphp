<?php
use MyEasyPHP\Libs\Html;//common for all view files
?>
<?php
    Html::loadAssets("gentelella/vendors/bootstrap/dist/css/bootstrap.min.css");
    Html::loadAssets("gentelella/vendors/font-awesome/css/font-awesome.min.css");
    Html::loadAssets("gentelella/vendors/nprogress/nprogress.css");
    Html::loadAssets("gentelella/build/css/custom.min.css");
?>

<div class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>Gentelella Alela!</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                  <img src="<?= Html::getImage("img.jpg") ?>" alt="..." class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2>John Doe</h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <?php
            Html::include('PartialViewFiles/Gentelella/sidebar_menu');
            ?>
            <!-- end sidebar menu -->            
          </div>
        </div>

        <!-- top navigation -->
        <?php Html::include('PartialViewFiles/Gentelella/top_nav'); ?>
        <!-- end top navigation -->
        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Plain Page</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5   form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12  ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Plain Page</h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Settings 1</a>
                            <a class="dropdown-item" href="#">Settings 2</a>
                          </div>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                      Add content to the page ...
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->
        

        <!-- footer content -->
        <?php
          Html::include('PartialViewFiles/Gentelella/footer');
        ?>
        <!-- /footer content -->
    </div>
</div>
<?php
//<!-- jQuery -->
Html::loadAssets("gentelella/vendors/jquery/dist/jquery.min.js");
//<!-- Bootstrap -->
Html::loadAssets("gentelella/vendors/bootstrap/dist/js/bootstrap.bundle.min.js");
//FastClick
Html::loadAssets("gentelella/vendors/fastclick/lib/fastclick.js");

//NProgress
Html::loadAssets("gentelella/vendors/nprogress/nprogress.js");

//<!-- Custom Theme Scripts -->
Html::loadAssets("gentelella/build/js/custom.min.js");
?>