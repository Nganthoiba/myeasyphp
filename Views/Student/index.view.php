<?php
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");

$resp = $viewData->response;
?>
<link href="<?= Config::get('css') ?>/carousel.css" rel="stylesheet" type="text/css"/>
<div class="jumbotron">
    <div class="container">
        <h2>List of Students:</h2>
        <p><a href="<?= getHtmlLink("Student", "add") ?>">Add</a></p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Roll Number</th>
                    <th>Section</th>
                    <th>School</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($resp->status == false){
                ?>
                <tr>
                    <td colspan="6"><?= $resp->msg ?></td>
                </tr>
                <?php
                }
                else{
                    $students = $resp->data;
                    foreach ($students as $student){
                ?>
                <tr>
                    <td><?= $student->student_id ?></td>
                    <td><?= $student->student_name ?></td>
                    <td><?= $student->class ?></td>
                    <td><?= $student->roll_number ?></td>
                    <td><?= $student->section ?></td>
                    <td><?= $student->school_name ?></td>
                    <td>
                        <a class="btn btn-link btn-sm" href="<?= getHtmlLink("Student", "edit", $student->student_id) ?>">Edit</a> | 
                        <a class="btn btn-link btn-sm" href="<?= getHtmlLink("Student", "confirmDelete", $student->student_id) ?>">Delete</a>
                        
                    </td>
                </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
