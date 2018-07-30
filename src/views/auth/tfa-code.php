<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 02.04.18
 * Time: 21:33
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\web\TfaCodeForm $model */
?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'wrapper' => 'col-sm-8'
        ]
    ]
]) ?>
<?php $form->errorSummary($model) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3><?= \Yii::t('dashboard', 'dvuh faktornaya') ?></h3>
    </div>

    <div class="panel-body">
        <?= $form->field($model, 'code', [
            'inputOptions' => [
                'autofocus' => 'true',
                'autocomplete' => 'off'
            ]
        ])->textInput() ?>

        <div class="form-group pull-right">
            <?= Html::submitButton(\Yii::t('dashboard', 'otpravit'), ['class' => 'btn btn-primary']) ?>
            <?= ' &nbsp; ' ?>
            <?= Html::a(
                \Yii::t('dashboard', 'vernutsa') . ' ' . \Yii::t('dashboard', 'na stranicu vhoda'),
                ['auth/login'],
                ['class' => 'btn btn-default']
            ) ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="panel-footer">
        <?= \Yii::t('dashboard', 'proverte pochtu s kodom') ?>
    </div>
</div>

<?php ActiveForm::end() ?>
