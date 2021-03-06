<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var $this yii\web\View */
/** @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var <?= \ltrim($generator->modelClass, '\\') ?> $model */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= "<?= " ?>Html::a(\Yii::t('yii', 'Update'), ['update', <?= $urlParams ?>], [
                    'class' => 'btn btn-primary js_show_progress'
                ]) ?>
                <?= "<?= " ?>Html::a(\Yii::t('yii', 'Delete'), ['delete', <?= $urlParams ?>], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post'
                    ]
                ]) ?>
            </p>

            <?= "<?= " ?>DetailView::widget([
                'model' => $model,
                'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "                '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "                '" . $column->name . ($format === 'text' ? '' : ':' . $format) . "',\n";
    }
}
?>
                ]
            ]) ?>

        </div>
    </div>
</div>
