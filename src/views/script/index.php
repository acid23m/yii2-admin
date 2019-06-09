<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\option\web\Script $model */
/** @var ActiveForm $form */

$this->title = \Yii::t('dashboard', 'skripty');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="script-update">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= \Yii::t('dashboard', 'zdes mozhno') ?>
            </p>

            <p class="alert alert-warning">
                <?= \mb_strtoupper(\Yii::t('dashboard', 'buddte vnimatelni')) ?>
            </p>

            <div class="panel panel-default">
                <?php $form = ActiveForm::begin() ?>

                <div class="panel-body">
                    <?php $form->errorSummary($model) ?>

                    <?= $form->field($model, 'head_script')->textarea([
                        'placeholder' => '<script></script>'
                    ]) ?>

                    <?= $form->field($model, 'body_script')->textarea([
                        'placeholder' => '<script></script>'
                    ]) ?>
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
