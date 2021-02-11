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
                    <th>Father Name</th>
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
                    <td><?= $student->Student_id ?></td>
                    <td><?= $student->student_name ?></td>
                    <td><?= $student->fathername ?></td>
                    <td><?= $student->class ?></td>
                    <td><?= $student->Roll_Number ?></td>
                    <td><?= $student->section ?></td>
                    <td><?= $student->School_Name ?></td>
                    <td>
                        <a class="btn btn-link btn-sm" href="<?= getHtmlLink("Student", "edit", $student->Student_id) ?>">Edit</a> | 
                        <a class="btn btn-link btn-sm" href="<?= getHtmlLink("Student", "confirmDelete", $student->Student_id) ?>">Delete</a>
                        
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
