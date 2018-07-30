<?php

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\web\User $model */

$this->title = \Yii::t('dashboard', 'dobavit polzovatelya');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('dashboard', 'polzovateli'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-record-create">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
