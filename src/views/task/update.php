<?php

/** @var \yii\web\View $this */
/** @var \dashboard\models\task\web\Task $model */

$this->title = \Yii::t('dashboard', 'obnovit zapis') . ': ' . $model->name;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('dashboard', 'zadaniya'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('yii', 'Update');
?>

<div class="task-update">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
