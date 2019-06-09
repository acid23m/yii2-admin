<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\web\PasswordResetRequestForm $model */
/** @var \dashboard\controllers\web\BaseController $controller */
$controller = $this->context;
?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9'
        ]
    ]
]) ?>
<?php $form->errorSummary($model) ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3><?= \Yii::t('dashboard', 'zapros na sbros parolya') ?></h3>
    </div>

    <div class="panel-body">
        <?= $form->field($model, 'email', [
            'inputOptions' => [
                'autofocus' => 'true',
                'autocomplete' => 'off'
            ]
        ])->input('email') ?>


        <div class="form-group pull-right">
            <?= Html::submitButton(\Yii::t('dashboard', 'otpravit'), ['class' => 'btn btn-primary']) ?>
            <?= ' &nbsp; ' ?>
            <?= ButtonDropdown::widget([
                'label' => \Yii::t('dashboard', 'vernutsa'),
                'options' => ['class' => 'btn btn-default'],
                'dropdown' => [
                    'items' => [
                        [
                            'label' => \Yii::t('dashboard', 'na stranicu vhoda'),
                            'url' => Url::to(["/{$controller->module->id}/auth/login"])
                        ],
                        [
                            'label' => \Yii::t('dashboard', 'na glavnuyu sayta'),
                            'url' => \Yii::$app->get('urlManagerFrontend')->createUrl(['/site/index'])
                        ]
                    ]
                ]
            ]) ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="panel-footer">
        <?= \Yii::t('dashboard', 'na ukazanniy adres budet vyslanna ssilka') ?>
    </div>
</div>

<?php ActiveForm::end() ?>
