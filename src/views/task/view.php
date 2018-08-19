<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var \dashboard\models\task\web\Task $model */

$this->title = $model->name;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('dashboard', 'zadaniya'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$status_list = $model->getList('statuses');
?>

<div class="task-view">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= Html::a(\Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                    'class' => 'btn btn-primary js_show_progress'
                ]) ?>
                <?= Html::a(\Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post'
                    ]
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'min',
                    'hour',
                    'day',
                    'month',
                    'weekDay',
                    'command',
                    [
                        'attribute' => 'status',
                        'value' => $status_list()[$model->status]
                    ]
                ]
            ]) ?>

        </div>
    </div>
</div>
