<?php 
use MyEasyPHP\Libs\Config;
Config::set('page_title',$viewData->httpStatus);
$css_class = $viewData->httpCode == 500?"text-danger":"text-warning";
?>
<h1> <?= $viewData->httpCode." ".$viewData->httpStatus ?></h1>
<p><b>Details:</b> <?= $viewData->details ?></p>
<p>
We will work on fixing that right away.
Meanwhile, you may <a href="<?= Config::get('host') ?>/">return to home</a> or try using the search form.
</p>