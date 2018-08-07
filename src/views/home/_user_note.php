<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 07.08.18
 * Time: 22:46
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
?>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>
                    <i class="fa fa-file-text-o"></i>
                    <?= \Yii::t('dashboard', 'zapisi') ?>
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li>
                        <a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php $form = ActiveForm::begin() ?>

                <?= $form->field($user, 'note')
                    ->label(false)
                    ->textarea() ?>

                <?= Html::submitButton(\Yii::t('dashboard', 'sohranit'),
                    ['class' => 'btn btn-success js_show_progress']) ?>

                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
