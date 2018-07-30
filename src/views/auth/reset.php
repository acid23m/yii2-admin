<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\web\ResetPasswordForm $model */
?>

<?php $form = ActiveForm::begin() ?>
<?php $form->errorSummary($model) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3><?= Yii::t('dashboard', 'sbros parolya') ?></h3>
    </div>

    <div class="panel-body">
        <?= $form->field($model, 'password', [
            'inputOptions' => [
                'autofocus' => 'true',
                'autocomplete' => 'off'
            ]
        ])->passwordInput() ?>


        <div class="form-group pull-right">
            <?= Html::submitButton(Yii::t('dashboard', 'otpravit'), ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<?php ActiveForm::end() ?>
