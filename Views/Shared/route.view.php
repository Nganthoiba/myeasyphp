<?php 
use MyEasyPHP\Libs\Html;
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
<div class="container">
    <H1>Routes:</H1>
    <table class="table table-info">
        <p>List of available routes:</p>
        <thead>
            <tr>
                <th>Sl. No.</th>
                <th>URL</th>
                <th>Controller Name</th>
                <th>Action Name</th>
                <th>Is Only function(y/n)</th>
                <th>HTTP methods allowed</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $k = 1;//indexing
            foreach($routes as $route){
            ?>
            <tr>
                <td><?= $k++ ?></td>
                <td><?= $route->getPath() ?></td>
                <td><?= $route->getController() ?></td>
                <td><?= $route->getAction() ?></td>
                <td><?= $route->isFunction()?"Y":"N" ?></td>
                <td><?= implode(', ',$route->getMethods()) ?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
