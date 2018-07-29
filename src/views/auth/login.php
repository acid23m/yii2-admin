<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 29.07.18
 * Time: 21:57
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\web\LoginForm $model */
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
        <h3><?= \Yii::t('dashboard', 'dostup ogranichen') ?></h3>
    </div>

    <div class="panel-body">
        <?= $form->field($model, 'username', [
            'inputOptions' => [
                'autofocus' => 'true'
            ]
        ])->textInput(['maxlength' => true]) ?>


        <?= $form->field($model, 'password', [
            'inputOptions' => [
                'maxlength' => STRING_LENGTH_SHORT,
                'autocomplete' => 'off'
            ]
        ])->passwordInput() ?>


        <?= $form->field($model, 'rememberMe')->checkbox() ?>


        <div class="form-group pull-right">
            <?= Html::submitButton(\Yii::t('dashboard', 'voyti'), ['class' => 'btn btn-primary']) ?>
            <?= ' &nbsp; ' ?>
            <?= Html::a(
                \Yii::t('dashboard', 'vernutsa na sayt'),
                \Yii::$app->get('urlManagerFrontend')->createUrl(['/site/index']),
                ['class' => 'btn btn-link']
            ) ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="panel-footer">
        <?= \Yii::t('dashboard', 'esli zabili parol', [
            'sbrosit' => Html::a(\Yii::t('dashboard', 'sbrosit'), ['auth/request-password-reset'])
        ]) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
