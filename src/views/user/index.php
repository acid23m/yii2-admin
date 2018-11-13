<?php

use dashboard\models\user\web\User;
use dashboard\models\user\web\UserSearch;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yiister\gentelella\widgets\grid\GridView;

/** @var \yii\web\View $this */
/** @var UserSearch $searchModel */
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = \Yii::t('dashboard', 'polzovateli');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;

$status_list = $searchModel->getList('statuses');
?>

<div class="user-record-index">

    <p>
        <?= Html::a(\Yii::t('dashboard', 'dobavit polzovatelya'), ['create'], [
            'class' => 'btn btn-success js_show_progress'
        ]) ?>
    </p>

    <?= GridView::widget([
        'id' => 'user-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'condensed' => true,
        'columns' => [
            'id',
            [
                'attribute' => 'username',
                'value' => function (User $model, $key, $index) {
                    return (int) \Yii::$app->user->id === (int) $model['id']
                        ? Html::tag('strong', $model['username'])
                        : $model['username'];
                },
                'format' => 'html'
            ],
            'email:email',
            [
                'attribute' => 'role',
                'value' => function (User $model, $key, $index) {
                    return $model->getRoles(true, true)[$model->role];
                },
                'filter' => $searchModel->getRoles(true)
            ],
            [
                'attribute' => 'status',
                'value' => function (User $model, $key, $index) use ($status_list) {
                    return Html::tag('span', $status_list()[$model->status]);
                },
                'format' => 'html',
                'filter' => $status_list()
            ],
            [
                'class' => ActionColumn::class,
                'buttons' => [
                    'delete' => function ($url, User $model, $key) {
                        $options = [
                            'title' => \Yii::t('yii', 'Delete'),
                            'aria-label' => \Yii::t('yii', 'Delete'),
                            'data-confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0'
                        ];

                        return (int) \Yii::$app->user->id !== (int) $model->id
                            ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options)
                            : '';
                    }
                ],
                'buttonOptions' => ['class' => 'js_show_progress'],
                'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;']
            ]
        ]
    ]) ?>

</div>
