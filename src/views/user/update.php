<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \backend\modules\access\models\User $model */

$this->title = \Yii::t('access/user', 'Obnovit polzovatelya') . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('access/user', 'Polzovateli'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('yii', 'Update');
?>

<div class="user-record-update">

    <?= $this->render('_form', [
        'model' => $model,
        'title' => Html::encode($this->title)
    ]) ?>

</div>
