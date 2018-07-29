<?php

use common\modules\file\models\Base64Img;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var \backend\modules\access\models\User $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('access/user', 'Polzovateli'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$status_list = $model->getList('statuses');
?>

<div class="user-record-view">

    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('yii', 'Update'), ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary js_show_progress']) ?>
        <?= Html::a(\Yii::t('access/user', 'Obnovit token'), ['update-token', 'id' => $model->id],
            ['class' => 'btn btn-default js_show_progress']) ?>
        <?= (\Yii::$app->user->id != $model->id)
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
            [
                'attribute' => 'avatar',
                'format' => 'raw',
                'value' => Html::img(Base64Img::thumb($model->avatar, 200, 0, Base64Img::OUTPUT_AS_DATA_URI))
            ],
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
