<?php 
use MyEasyPHP\Libs\Config;
Config::set('page_title',$viewData->httpStatus);
$css_class = $viewData->httpCode == 500?"text-danger":"text-warning";
?>

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1> <?= $viewData->httpCode." ".$viewData->httpStatus ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active"><?= $viewData->httpCode." ".$viewData->httpStatus ?></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="error-page">
        <h2 class="headline <?= $css_class ?>"><?= $viewData->httpCode ?></h2>

        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle <?= $css_class ?>"></i> Oops! <?= $viewData->httpStatus ?>.</h3>
          
            <p>
              We will work on fixing that right away.
              Meanwhile, you may <a href="<?= Config::get('host') ?>/">return to home</a> or try using the search form.
            </p>
            <?php 
            if(Config::get('error_display')==true){
            ?>
                <p><b>Details:</b> <?= $viewData->details ?></p>
            <?php             
            }
            ?>
          
        </div>
      </div>
      <!-- /.error-page -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->