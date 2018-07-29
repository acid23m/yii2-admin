<?php

use kartik\icons\Icon;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \backend\modules\access\models\User $model */
/** @var \kartik\widgets\ActiveForm $form */
/** @var string $title */

Icon::map($this);

$status_list = $model->getList('statuses');
?>

<div class="user-record-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]) ?>
    <?php $form->errorSummary($model) ?>

    <fieldset>
        <legend><?= $title ?></legend>

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'username', [
                    'addon' => [
                        'prepend' => ['content' => Icon::show('user')]
                    ]
                ])->textInput(['maxlength' => true]) ?>


                <?= $form->field($model, 'password', [
                    'addon' => [
                        'prepend' => ['content' => Icon::show('lock')]
                    ]
                ])->passwordInput(['maxlength' => true]) ?>


                <?= $form->field($model, 'email', [
                    'addon' => [
                        'prepend' => ['content' => Icon::show('envelope')]
                    ]
                ])->textInput(['maxlength' => true]) ?>


                <?php $model->tfa = (int) $model->tfa ?>
                <?= $form->field($model, 'tfa')->checkbox() ?>


                <?= $form->field($model, 'role', [
                    'addon' => [
                        'prepend' => ['content' => Icon::show('authentication-keyalt')]
                    ]
                ])->dropDownList(
                    $model->getRoles(true, \Yii::$app->user->can($model::ROLE_SUPER)),
                    \Yii::$app->user->can($model::ROLE_ADMIN) ? [] : ['disabled' => 'disabled']
                ) ?>
            </div>


            <div class="col-xs-12 col-md-6">
                <?= $form->field($model, 'avatar_file')->widget(FileInput::class, [
                    'options' => [
                        'accept' => 'image/*'
                    ],
                    'pluginOptions' => [
                        'previewFileType' => 'image',
                        'showUpload' => false,
                        'browseClass' => 'btn btn-default',
                        'initialPreview' => empty($model->avatar) || null === $model->avatar ? [] : [$model->avatar],
                        'initialPreviewAsData' => true,
                        'overwriteInitial' => false
                    ]
                ]) ?>


                <?php $model->status = (int) $model->status ?>
                <?= $form->field($model, 'status', [
                    'addon' => [
                        'prepend' => ['content' => Icon::show('off')]
                    ]
                ])->dropDownList($status_list()) ?>
            </div>
        </div>


        <div class="form-group well">
            <?= Html::submitButton($model->isNewRecord ? \Yii::t('common', 'Dobavit') : \Yii::t('common', 'Obnovit'),
                ['class' => $model->isNewRecord ? 'btn btn-success js_show_progress' : 'btn btn-primary js_show_progress']) ?>
        </div>

    </fieldset>

    <?php ActiveForm::end() ?>

</div>
