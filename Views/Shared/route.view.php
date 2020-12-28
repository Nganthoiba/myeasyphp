<H1>Routes:</H1>
<?php 
/*if(!is_null($model)){
    echo "<pre>";
    print_r($model);
    echo "</pre>";
}*/
?>
<table border="1">
    <caption>List of available routes:</caption>
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
        foreach($routes as $k=>$route){
        ?>
        <tr>
            <td><?= $k+1 ?></td>
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

