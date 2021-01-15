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
            <label><?= $model->getPropertyDisplayName('Name') ?>:</label>
            <?php Html::textField($model, 'Name','form-control'); ?>
        </div>
        <div class="form-group">
            <label><?= $model->getPropertyDisplayName('Email') ?></label>
            <?php Html::textField($model, 'Email','form-control'); ?>
        </div>
        <div class="form-group">
            <label><?= $model->getPropertyDisplayName('Sex') ?>:</label>
            <?php Html::radioButtons($model, 'Sex', [
                ["name"=>"Male","value"=>"M"],
                ["name"=>"Female","value"=>"F"],
            ]); ?>
        </div> 
        <div class="form-group">
            <label><?= $model->getPropertyDisplayName('Body') ?>:</label>
            <?php Html::textareaField($model, 'Body', 5, 50, 'form-control'); ?>
        </div> 
        
        <button type="submit" class="btn btn-success">Submit</button>
        <?php Html::endForm(); ?>
    </div>
</div>




