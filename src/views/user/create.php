<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \backend\modules\access\models\User $model */

$this->title = \Yii::t('access/user', 'Dobavit polzovatelya');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('access/user', 'Polzovateli'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-record-create">

    <?= $this->render('_form', [
        'model' => $model,
        'title' => Html::encode($this->title)
    ]) ?>

</div>
