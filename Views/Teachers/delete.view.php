<?php
use MyEasyPHP\Libs\Html;
Html::setContainer('_masterPage');
?>
<div class="container">
<?php
echo $viewData->response->msg;
?>
    <p>
        <a href="<?= Html::hyperlink('Teachers', 'read') ?>">Back to view teacher's list</a>
    </p>
</div>
