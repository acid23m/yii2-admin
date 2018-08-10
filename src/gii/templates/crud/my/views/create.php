<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var $this yii\web\View */
/** @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

/** @var \yii\web\View $this */
/** @var \<?= ltrim($generator->modelClass, '\\') ?> $model */

$this->title = \Yii::t('dashboard', 'dobavit zapis');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">

    <?= "<?= " ?>$this->render('_form', [
        'model' => $model
    ]) ?>

</div>
