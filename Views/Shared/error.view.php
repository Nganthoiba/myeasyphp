<?php 
use MyEasyPHP\Libs\Config;
Config::set('page_title',$viewData->httpStatus);
$css_class = $viewData->httpCode == 500?"text-danger":"text-warning";
?>
<h1> <?= $viewData->httpCode." ".$viewData->httpStatus ?></h1>
<p><b>Error:</b> <?= $viewData->ErrorMessage ?></p>
<?php 
if($viewData->ErrorDetail!=""){
?>
<p><h4>Details:</h4>
<?php
    $Errors = explode("#", $viewData->ErrorDetail);
    foreach($Errors as $error){
        echo $error."<br/>";
    }
}
?>
</p>