<?php
use MyEasyPHP\Libs\Html;
Html::setContainer("_masterPage");
?>

<div class="container">
    <p>List of teachers:</p>
    <table class="table table-light">
        <thead>
            <tr>
                <th>Teacher Number</th>
                <th>Class</th>
                <th>Teacher Name</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Specialized Subject</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($teachers as $teacher){
            ?>
            <tr>
                <td><?= $teacher->teacher_no ?></td>
                <td><?= $teacher->class ?></td>
                <td><?= $teacher->name ?></td>
                <td><?= $teacher->address ?></td>
                <td><?= $teacher->contact_no ?></td>
                <td><?= $teacher->subject ?></td>  
                <td><a href="<?= Html::hyperlink("Teachers", 'update', [$teacher->teacher_no,$teacher->class]) ?>">Edit</a></td>
                <td><a href="<?= Html::hyperlink("Teachers", 'delete', [$teacher->teacher_no,$teacher->class]) ?>">Delete</a></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <a href="create">Add</a>
</div>

