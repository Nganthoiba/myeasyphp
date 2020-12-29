<?php 
use MyEasyPHP\Libs\Config;
Config::set('page_title',$viewData->httpStatus);
$css_class = $viewData->httpCode == 500?"text-danger":"text-warning";
?>
<h1> <?= $viewData->httpCode." ".$viewData->httpStatus ?></h1>

<?php
if(Config::get("error_display")){
    //Debugging is granted only if error_display is set to true.
    //This flag must be set to true only when development is in progress.
    //Beware that, when the code is in production server, it must be set
    //to false.
?>
<p><b>Error:</b> <?= $viewData->ErrorMessage ?></p>
    <?php 
    if($viewData->ErrorDetail!=""){
    ?>
<p><h4>Details:</h4>
    <ol>
    <?php
        $Errors = explode("#", $viewData->ErrorDetail);
        $i=0;
        foreach($Errors as $error){            
            if(trim($error) == ""){
                continue;
            }        
            if($i < sizeof($Errors)-2){
                echo "<li>".substr($error,2)."</li>";
            }            
            $i++;
        }
    }
    ?>
    </ol>
</p>
<?php
} 
?>