<?php

use dashboard\models\task\web\Task;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yiister\gentelella\widgets\grid\GridView;

/** @var \yii\web\View $this */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var Task $model */
$model = $dataProvider->getModels()[0] ?? new Task;

$this->title = \Yii::t('dashboard', 'zadaniya');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
    (function ($) {
        "use strict";

        const $grid = $("#post-grid");
        const $delBtn = $("#del-multi-btn");
        
        $grid.on("change", "input[name^=selection]", () => {
            let keys = $grid.yiiGridView("getSelectedRows");

            if (keys.length > 0) {
                $delBtn
                    .removeAttr("disabled")
                    .attr("href", "' . Url::to(['delete-multiple']) . '?ids=" + JSON.stringify(keys));
            } else {
                $delBtn.attr("disabled", "disabled");
            }
        });
    })(jQuery);
', $this::POS_END);

$status_list = $model->getList('statuses');
?>

<div class="task-record-index">

    <p>
        <?= Html::a(\Yii::t('dashboard', 'dobavit zapis'), ['create'], [
            'class' => 'btn btn-success js_show_progress'
        ]) ?>
        <?= Html::a(\Yii::t('dashboard', 'udalit otmechennie'), ['delete-multiple'], [
            'id' => 'del-multi-btn',
            'class' => 'btn btn-danger',
            'disabled' => 'disabled',
            'data' => [
                'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
                'pjax' => 0
            ]
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => CheckboxColumn::class],
            ['class' => SerialColumn::class],

            'name',
            'min',
            'hour',
            'day',
            'month',
            'weekDay',
            [
                'attribute' => 'status',
                'value' => static function (Task $model, $key, $index) use ($status_list) {
                    return Html::tag('span', $status_list()[$model->status]);
                },
                'format' => 'html',
                'filter' => $status_list()
            ],

            [
                'class' => ActionColumn::class,
                'buttonOptions' => ['class' => 'js_show_progress'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;']
            ]
        ]
    ]) ?>

</div>
