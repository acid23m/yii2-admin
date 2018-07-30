<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var \dashboard\models\user\web\User $model */

$this->title = Html::encode($model->username);
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('dashboard', 'polzovateli'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$status_list = $model->getList('statuses');
?>

<div class="user-record-view">

    <p>
        <?= Html::a(\Yii::t('yii', 'Update'), ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary js_show_progress']) ?>
        <?= Html::a(\Yii::t('dashboard', 'obnovit token'), ['update-token', 'id' => $model->id],
            ['class' => 'btn btn-default js_show_progress']) ?>
        <?= (\Yii::$app->getUser()->id != $model->id)
            ? Html::a(\Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post'
                ]
            ])
            : null ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
//            [
//                'attribute' => 'avatar',
//                'format' => 'raw',
//                'value' => Html::img(Base64Img::thumb($model->avatar, 200, 0, Base64Img::OUTPUT_AS_DATA_URI))
//            ],
            [
                'attribute' => 'username',
                'format' => 'html',
                'value' => Html::tag('strong', $model->username)
            ],
            'email:email',
            'tfa:boolean',
            [
                'attribute' => 'role',
                'value' => $model->getRoles(true, true)[$model->role]
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => $model->status == $model::STATUS_ACTIVE
                    ? Html::tag('span', $status_list()[$model->status], ['class' => 'text-success'])
                    : Html::tag('span', $status_list()[$model->status], ['class' => 'text-danger'])
            ],
            'note:html',
            'access_token',
            'created_at:datetime',
            'updated_at:datetime',
            'last_access:datetime',
            'ip'
        ]
    ]) ?>

</div>
