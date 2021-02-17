<?php
use MyEasyPHP\Libs\Html;
use MyEasyPHP\Libs\Config;
Html::setContainer('_masterPage');
?>
<style>
    label{
        font-weight:bold;
    }
</style>
<div class="col-sm-4" style="margin:auto">
    <div class="text-center">
        <h2>Password Forgotten?</h2>
    </div>
    <form action="<?= Config::get('host') ?>/Accounts/generatePasswordResetCode" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control"
                   placeholder="Enter the email which was registered." required/>
        </div>
        <div class="text-center">
            <button class="btn btn-dark-green" type="submit">Submit</button>
        </div>
    </form>    
</div>

