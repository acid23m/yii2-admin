<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\web\User $model */

$this->title = \Yii::t('dashboard', 'obnovit polzovatelya') . ' ' . Html::encode($model->username);
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('dashboard', 'polzovateli'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->username), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('yii', 'Update');
?>

<div class="user-record-update">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
