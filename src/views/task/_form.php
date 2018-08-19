<?php

use imagetool\helpers\File;
use kartik\date\DatePicker;
use kartik\file\FileInput;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\task\web\Task $model */
/** @var ActiveForm $form */

$status_list = $model->getList('statuses');
$help_link = 'https://help.ubuntu.ru/wiki/cron';
?>

<div class="post-form">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <?php $form = ActiveForm::begin() ?>

                <div class="panel-body">
                    <?php $form->errorSummary($model) ?>


                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


                    <?= $form->field($model, 'min')->textInput(['maxlength' => true]) ?>


                    <?= $form->field($model, 'hour')->textInput(['maxlength' => true]) ?>


                    <?= $form->field($model, 'day')->textInput(['maxlength' => true]) ?>


                    <?= $form->field($model, 'month')->textInput(['maxlength' => true]) ?>


                    <?= $form->field($model, 'weekDay')->textInput(['maxlength' => true]) ?>


                    <?= $form->field($model, 'command')->textInput(['maxlength' => true]) ?>


                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <?php $model->status = (int) $model->status ?>
                            <?= $form->field($model, 'status')->dropDownList($status_list()) ?>
                        </div>
                    </div>

                </div>

                <div class="panel-footer">
                    <?= Html::submitButton($model->isNewRecord
                        ? \Yii::t('dashboard', 'sozdat')
                        : \Yii::t('dashboard', 'sohranit'), [
                            'class' => $model->isNewRecord
                                ? 'btn btn-success js_show_progress'
                                : 'btn btn-primary js_show_progress'
                        ]
                    ) ?>
                    <?= Html::a($help_link, $help_link, [
                        'class' => 'btn btn-link pull-right',
                        'target' => '_blank',
                        'rel' => 'nofollow noopener noreferrer'
                    ]) ?>
                </div>

                <?php ActiveForm::end() ?>
            </div>

        </div>
    </div>
</div>
