<?php

use dashboard\models\log\LogRecord;
use kartik\daterange\DateRangePicker;
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yiister\gentelella\widgets\grid\GridView;

/** @var yii\web\View $this */
/** @var \dashboard\models\log\LogSearch $logSearchModel */
/** @var \yii\data\ActiveDataProvider $logDataProvider */

$this->registerJs('
    (function ($) {
        "use strict";
        
        const $grid = $("#log-grid");
        const $delBtn = $("#del-log-btn");

        $grid.on("change", "input[name^=selection]", () => {
            let keys = $grid.yiiGridView("getSelectedRows");
            
            if (keys.length > 0) {
                $delBtn
                    .removeAttr("disabled")
                    .attr("href", "' . Url::to(['delete-log']) . '?ids=" + JSON.stringify(keys));
            } else {
                $delBtn.attr("disabled", "disabled");
            }
        });
    })(jQuery);
', $this::POS_END);

$level_list = $logSearchModel->getList('levels');
?>

<div class="row">
    <div class="col-xs-12">
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
                            <li>
                                <?= Html::a(\Yii::t('dashboard', 'sbrosit tablicu'), ['index']) ?>
                            </li>
                            <li>
                                <?= Html::a(\Yii::t('dashboard', 'skachat log'), ['download-log']) ?>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <p>
                    <?= Html::a(\Yii::t('dashboard', 'udalit vse'), ['home/clear-log'], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'method' => 'post'
                        ]
                    ]) ?>
                    <?= Html::a(\Yii::t('dashboard', 'udalit otmechennie'), ['delete-log'], [
                        'id' => 'del-log-btn',
                        'class' => 'btn btn-danger',
                        'disabled' => 'disabled',
                        'data' => [
                            'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'method' => 'post'
                        ]
                    ]) ?>
                </p>

                <?= GridView::widget([
                    'id' => 'log-grid',
                    'dataProvider' => $logDataProvider,
                    'filterModel' => $logSearchModel,
                    'condensed' => true,
                    'columns' => [
                        ['class' => CheckboxColumn::class],
//                        'id:integer',
                        [
                            'attribute' => 'level',
                            'value' => static function (LogRecord $model, $key, $index) use ($level_list) {
                                return $level_list()[$model->level];
                            },
                            'filter' => $level_list()
                        ],
                        'category',
//                        'prefix',
                        [
                            'attribute' => 'log_time',
                            'format' => 'datetime',
                            'filter' => DateRangePicker::widget([
                                'model' => $logSearchModel,
                                'attribute' => 'log_time',
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'locale' => [
                                        'format' => 'Y-m-d',
                                        'separator' => ','
                                    ]
                                ]
                            ])
                        ],
                        'message:ntext'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
