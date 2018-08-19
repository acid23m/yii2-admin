<?php

/** @var \yii\web\View $this */
/** @var \dashboard\models\task\web\Task $model */

$this->title = \Yii::t('dashboard', 'dobavit zapis');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('dashboard', 'zadaniya'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="task-create">

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
