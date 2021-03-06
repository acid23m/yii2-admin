<?php

use dashboard\models\user\web\User;
use imagetool\helpers\File;
use kartik\file\FileInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yiidreamteam\widgets\timezone\Picker;

/** @var \yii\web\View $this */
/** @var \dashboard\models\option\web\Main $model */
/** @var ActiveForm $form */

$this->title = \Yii::t('dashboard', 'osnovnie nastroyki');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;

$enctype_list = $model->getList('mail_gate_encryptions');
?>

<div class="option-update">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'enctype' => 'multipart/form-data'
                    ]
                ]) ?>

                <div class="panel-body">
                    <?php $form->errorSummary($model) ?>

                    <div class="row">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <?php $admin_lang_list = $model->getList('admin_langs') ?>
                            <?= $form->field($model, 'admin_lang')->dropDownList($admin_lang_list()) ?>
                        </div>
                    </div>


                    <?= $form->field($model, 'app_name')->textInput(['maxlength' => true]) ?>


                    <?php if (\Yii::$app->get('option')->app_logo !== ''): ?>
                        <!--<img src="" alt="">-->
                    <?php endif ?>
                    <?php //= $form->field($model, 'app_logo')->fileInput() ?>
                    <?= $form->field($model, 'app_logo')->widget(FileInput::class, [
                        'options' => ['accept' => 'image/*'],
                        'pluginOptions' => [
                            'previewFileType' => 'image',
                            'showUpload' => false,
                            'browseClass' => 'btn btn-default',
                            'initialPreview' => empty($model->get('app_logo')) || $model->get('app_logo') === null
                                ? []
                                : [File::getUrl($model->get('app_logo'))],
                            'initialPreviewAsData' => true,
                            'initialCaption' => 'Logo',
                            'overwriteInitial' => false
                        ]
                    ]) ?>


                    <div class="row">
                        <div class="col-xs-12 col-md-6 col-lg-4">
                            <?= $form->field($model, 'time_zone')->widget(Picker::class, [
                                'sortBy' => Picker::SORT_OFFSET,
                                'options' => ['class' => 'form-control']
                            ]) ?>
                        </div>
                    </div>


                    <br><br>
                    <h4>
                        <?= \Yii::t('dashboard', 'pochtoviy shluz') ?>
                        <small><?= \Yii::t('dashboard', 'teh pochta') ?></small>
                    </h4>
                    <br>

                    <?= $form->field($model, 'mail_gate_host')->textInput(['maxlength' => true]) ?>


                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'mail_gate_login')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'mail_gate_password')->passwordInput(['maxlength' => true]) ?>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'mail_gate_port')->textInput() ?>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'mail_gate_encryption')
                                ->dropDownList($enctype_list(), ['prompt' => '']) ?>
                        </div>
                    </div>


                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <?= $form->field($model, 'maintenance_mode')->checkbox() ?>
                        </div>
                    </div>


                    <?php if (\Yii::$app->getUser()->can(User::ROLE_SUPER)): ?>

                        <hr>

                        <?= $form->field($model, 'site_status')
                            ->checkbox()
                            ->hint(\Yii::t('dashboard', 'v doverennie ip')) ?>


                        <?= $form->field($model, 'white_ips')
                            ->textInput(['maxlength' => true])
                            ->hint(\Yii::t('dashboard', 'spisok ip')) ?>


                        <?= $form->field($model, 'black_ips')
                            ->textInput(['maxlength' => true])
                            ->hint(\Yii::t('dashboard', 'spisok ip')) ?>

                    <?php endif ?>
                </div>

                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::submitButton(\Yii::t('dashboard', 'obnovit'),
                            ['class' => 'btn btn-primary js_show_progress']) ?>
                    </div>
                </div>

                <?php ActiveForm::end() ?>
            </div>

        </div>
    </div>
</div>
