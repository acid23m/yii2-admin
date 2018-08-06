<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 06.08.18
 * Time: 0:54
 */

use yii\grid\CheckboxColumn;
use yiister\gentelella\widgets\grid\GridView;

/** @var yii\web\View $this */
/** @var \dashboard\models\log\LogSearch $logSearchModel */
/** @var \yii\data\ActiveDataProvider $logDataProvider */

$level_list = $logSearchModel->getList('levels');
?>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>
                    <i class="fa fa-bug"></i>
                    <?= \Yii::t('dashboard', 'log oshibok') ?>
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Settings 1</a></li>
                            <li><a href="#">Settings 2</a></li>
                        </ul>
                    </li>
                    <li>
                        <a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?= GridView::widget([
                    'id' => 'log-grid',
                    'dataProvider' => $logDataProvider,
                    'filterModel' => $logSearchModel,
                    'columns' => [
                        ['class' => CheckboxColumn::class],
                        'id:integer',
                        [
                            'attribute' => 'level',
                            'value' => function ($model, $key, $index) use ($level_list) {
                                /** @var \dashboard\models\log\LogSearch $model */
                                return $level_list()[$model->level];
                            },
                            'filter' => $level_list()
                        ],
                        'category',
//                        'prefix',
                        'log_time:datetime',
                        'message:ntext'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
