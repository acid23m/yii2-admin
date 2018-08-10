<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

/** @var \yii\web\View $this */
/** @var \<?= ltrim($generator->modelClass, '\\') ?> $model */

$this->title = \Yii::t('dashboard', 'obnovit zapis') . ': ' . $model-><?= $generator->getNameAttribute() ?>;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = \Yii::t('yii', 'Update');
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">

    <?= "<?= " ?>$this->render('_form', [
        'model' => $model
    ]) ?>

</div>
