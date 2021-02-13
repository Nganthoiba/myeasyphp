<?php 
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Debugging\DisplaySourceCode;
/*
 * This is the error view file.
 * Warning: Don't delete this file by mistake, be careful.
 */
Config::set('page_title',$viewData->httpStatus);
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?= Config::get('page_title') ?></title>
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
        <?php
        Html::include('Shared/mdbNavbar');
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
                    echo "<H5>Error Line:</H5>({$viewData->filePath})";
                    $startLine = ($viewData->lineNo-4)<1?1:$viewData->lineNo-4;
                    $endLine = ($viewData->lineNo+4);
                    DisplaySourceCode::display($viewData->filePath,$startLine,$endLine,$viewData->lineNo);
                }
                ?>

            </div>
        </div>
        <?php
        } 
        Html::loadJs([
            "bootstrap",
            "mdb"
        ]);
        ?>
    </body>
    
</html>
