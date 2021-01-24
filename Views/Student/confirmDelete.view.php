<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");
?>
<link href="<?= Config::get('css') ?>/carousel.css" rel="stylesheet" type="text/css"/>
<style>
    .row{
        margin-bottom: 10px;
    }
</style>
<?php
$student = $viewData->response->data;
?>
<div class="jumbotron">
    <div class="container">
        <h1 class="display-3">Are you sure to delete?</h1>
        <form action="<?= getHtmlLink("Student", "delete", $student->student_id) ?>" method="POST">
            <input type="hidden" value="<?= $student->student_id ?>" name="student_id" />
            <button type="submit" class="btn btn-primary">Yes</button>
            <a class="btn btn-info" href="<?= getHtmlLink("Student", "index") ?>">No</a>
        </form>
        <div><?= $viewData->response->msg ?></div>
    </div>
</div>

