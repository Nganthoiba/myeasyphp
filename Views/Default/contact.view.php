<?php 
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Config;
Html::setContainer('_masterPage');
?>
<div class="container">
    <h2>Contact us.</h2>
    <div>
        <?php Html::beginForm(Config::get('host').'/Contact','POST'); ?>
        <div class="form-group">
            <?php Html::label($model, 'Name') ?>
            <?php Html::textField($model, 'Name',['class'=>'form-control']); ?>
        </div>
        <div class="form-group">
            <?php Html::label($model, 'Email') ?>
            <?php Html::textField($model, 'Email',['class'=>'form-control']); ?>
        </div>
        <div class="form-group">
            <?php Html::label($model, 'Sex') ?>
            <?php Html::radioButtons($model, 'Sex', [
                ["name"=>"Male","value"=>"M"],
                ["name"=>"Female","value"=>"F"],
            ]); ?>
        </div> 
        <div class="form-group">
            <?php Html::label($model, 'Body') ?>
            <?php Html::textareaField($model, 'Body', ['class'=>'form-control']); ?>
        </div>         
        <button type="submit" class="btn btn-success">Submit</button>
        <?php Html::endForm(); ?>
    </div>
</div>




