<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");
?>
<?php
$resp = $viewData->response;
$student = $model;
?>
<link href="<?= Config::get('css') ?>/carousel.css" rel="stylesheet" type="text/css"/>
<style>
    .row{
        margin-bottom: 10px;
    }
</style>
<div class="jumbotron">
    <div class="container">
        <h2 class="display-3">Update Student</h2>
        <form method="POST">
            <div class="row">
                <div class="col-sm-3">
                    <label>Student Name:</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" name="student_name" value="<?= $student->student_name ?>" class="form-control" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <label>Class:</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" name="class" value="<?= $student->class ?>" class="form-control" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <label>Roll Number:</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" name="roll_number" value="<?= $student->roll_number ?>" class="form-control" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <label>Section:</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" name="section" value="<?= $student->section ?>" class="form-control" />
                </div>
            </div>        
            <div class="row">
                <div class="col-sm-3">
                    <label>School name:</label>
                </div>
                <div class="col-sm-4">
                    <input type="text" name="school_name" value="<?= $student->school_name ?>" class="form-control" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    &nbsp;
                </div>
                 <div class="col-sm-4">
                    <button class="btn btn-primary btn-lg" role="button">Submit &raquo;</button>
                </div>
            </div>
        </form>
        <p><?= $viewData->response->msg??"" ?></p>
    </div>
</div>

