<?php
use MyEasyPHP\Libs\Html;
Html::setContainer('_masterPage');

if(is_null($model)){
    die("Teacher not available.");
}
?>
<div class="container">
    <h3>Add teacher</h3>
    <?php Html::beginForm("Teachers/update/{$model->teacher_no}/{$model->class}", 'POST', 'form-horizontal') ?>
        <div class="row">
            <div class="col-sm-3">
                <?php Html::label($model, 'teacher_no'); ?>
            </div>
            <div class="col-sm-4">
                <?php Html::textField($model, 'teacher_no',['class'=>'form-control']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?php Html::label($model, 'class'); ?>
            </div>
            <div class="col-sm-4">
                <?php Html::textField($model, 'class',['class'=>'form-control']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?php Html::label($model, 'name'); ?>
            </div>
            <div class="col-sm-4">
                <?php Html::textField($model, 'name',['class'=>'form-control']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?php Html::label($model, 'address'); ?>
            </div>
            <div class="col-sm-4">
                <?php Html::textField($model, 'address',['class'=>'form-control']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?php Html::label($model, 'contact_no'); ?>
            </div>
            <div class="col-sm-4">
                <?php Html::textField($model, 'contact_no',['class'=>'form-control']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?php Html::label($model, 'subject'); ?>
            </div>
            <div class="col-sm-4">
                <?php Html::textField($model, 'subject',['class'=>'form-control']); ?>
            </div>
        </div>
        <button type="submit" class="btn btn-default">Update</button>
        <a href="<?= Html::hyperlink('Teachers','read') ?>" class="btn btn-link">View List</a>
        <div>
            <?php
            echo $viewData->response->msg;
            $errors = $viewData->response->error;
            if(is_array($errors)){
                foreach($errors as $error){
                    echo '<br/>'.$error;
                }
            }
            else{
                echo '<br/>'.$errors;
            }
            ?>
        </div>
        
    <?php Html::endForm(); ?>
    
</div>


