<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 08.08.18
 * Time: 14:39
 */

use yii\bootstrap\Tabs;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
?>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>
                    <i class="fa fa-plug"></i>
                    <?= \Yii::t('dashboard', 'obsluzhivanie') ?>
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
                <?= Tabs::widget([
                    'items' => [
                        [
                            'label' => \Yii::t('dashboard', 'kesh'),
                            'content' => $this->render('_service_cache'),
                            'active' => true
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
