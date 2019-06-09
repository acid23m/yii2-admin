<?php

use yii\bootstrap\Tabs;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
/** @var bool $search_index_is_active */
?>

<div class="row">
    <div class="col-xs-12">
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
                <?php
                $items = [
                    [
                        'label' => \Yii::t('dashboard', 'kesh'),
                        'content' => $this->render('_service_cache'),
                        'active' => true
                    ],
                    [
                        'label' => \Yii::t('dashboard', 'resursi'),
                        'content' => $this->render('_service_assets')
                    ],
                    [
                        'label' => \Yii::t('dashboard', 'karta sayta'),
                        'content' => $this->render('_service_sitemap')
                    ],
                    [
                        'label' => \Yii::t('dashboard', 'baza dannih'),
                        'content' => $this->render('_service_bd', compact('user'))
                    ]
                ];

                if ($search_index_is_active) {
                    $items[] = [
                        'label' => \Yii::t('dashboard', 'poisk'),
                        'content' => $this->render('_service_index')
                    ];
                }
                ?>

                <?= Tabs::widget([
                    'items' => $items,
                    'options' => [
                        'class' => 'bar_tabs'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
