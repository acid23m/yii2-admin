<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.08.18
 * Time: 1:14
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\option\web\Metrica $model */
/** @var ActiveForm $form */

$this->title = \Yii::t('dashboard', 'metrika');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="metrica-update">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <?php $form = ActiveForm::begin() ?>

                <div class="panel-body">
                    <?php $form->errorSummary($model) ?>

                    <?= $form->field($model, 'google_analytics')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'yandex_metrika')->textInput(['maxlength' => true]) ?>
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
