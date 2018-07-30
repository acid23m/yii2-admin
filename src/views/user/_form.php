<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\web\User $model */
/** @var \yii\bootstrap\ActiveForm $form */

$status_list = $model->getList('statuses');
?>

<div class="user-record-form panel panel-default">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]) ?>

    <div class="panel-body">
        <?php $form->errorSummary($model) ?>

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>


                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>


                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>


                <?php $model->tfa = (int) $model->tfa ?>
                <?= $form->field($model, 'tfa')->checkbox() ?>


                <?= $form->field($model, 'role')->dropDownList(
                    $model->getRoles(true, \Yii::$app->getUser()->can($model::ROLE_SUPER)),
                    \Yii::$app->getUser()->can($model::ROLE_ADMIN) ? [] : ['disabled' => 'disabled']
                ) ?>
            </div>


            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'avatar_file')->fileInput() ?>


                <?php $model->status = (int) $model->status ?>
                <?= $form->field($model, 'status')->dropDownList($status_list()) ?>
            </div>
        </div>
    </div>

    <div class="panel-footer">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord
                ? \Yii::t('dashboard', 'dobavit')
                : \Yii::t('dashboard', 'obnovit'), [
                    'class' => $model->isNewRecord
                        ? 'btn btn-success js_show_progress'
                        : 'btn btn-primary js_show_progress'
                ]
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end() ?>

</div>
