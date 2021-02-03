<?php 
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Debugging\DisplaySourceCode;

Config::set('page_title',$viewData->httpStatus);

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
Html::include('Shared/mdbNavbar');
$css_class = $viewData->httpCode == 500?"text-danger":"text-warning";
?>
<div class="container">
    <h1 style="color:red;font-family: Times New Roman, tahoma"> <?= $viewData->httpCode." ".$viewData->httpStatus ?></h1>

<?php
if(Config::get("error_display")){
    //Debugging is granted only if error_display is set to true.
    //This flag must be set to true only when development is in progress.
    //Beware that, when the code is in production server, it must be set
    //to false.
?>
    <div style="padding: 5px; font-size: 11pt;">
        <p><b>Error:</b> <?= $viewData->ErrorMessage ?></p>
        <?php 
        if($viewData->ErrorDetail!=""){
        ?>        
        <h4>Details:</h4>
        <ol>
        <?php
            $Errors = explode("#", $viewData->ErrorDetail);        
            foreach($Errors as $error){            
                if(trim($error) == ""){
                    continue;
                }
                echo "<li>".substr($error,2)."</li>";                    
            }
            
        ?>
        </ol>
        <?php 
        }
        if(trim($viewData->filePath)!==""){
            echo "<H5>Error Line:</H5>";
            $startLine = ($viewData->lineNo-4)<1?1:$viewData->lineNo-4;
            $endLine = ($viewData->lineNo+4);
            DisplaySourceCode::display($viewData->filePath,$startLine,$endLine,$viewData->lineNo);
        }
        ?>
      
    </div>
</div>
<?php
} 
?>