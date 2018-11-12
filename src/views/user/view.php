<?php

use dashboard\models\user\web\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var User $model */

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
            'avatar:image',
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
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'value' => function (User $model, $widget) {
                    $dt = new \DateTime($model->created_at, new \DateTimeZone('UTC'));
                    $dt->setTimezone(new \DateTimeZone(\Yii::$app->getTimeZone()));

                    return $dt;
                }
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'value' => function (User $model, $widget) {
                    $dt = new \DateTime($model->updated_at, new \DateTimeZone('UTC'));
                    $dt->setTimezone(new \DateTimeZone(\Yii::$app->getTimeZone()));

                    return $dt;
                }
            ],
            [
                'attribute' => 'last_access',
                'format' => 'datetime',
                'value' => function (User $model, $widget) {
                    $dt = new \DateTime($model->last_access, new \DateTimeZone('UTC'));
                    $dt->setTimezone(new \DateTimeZone(\Yii::$app->getTimeZone()));

                    return $dt;
                }
            ],
            'ip'
        ]
    ]) ?>

</div>
