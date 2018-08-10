<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var yii\gii\generators\crud\Generator $generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;
use <?= $generator->indexWidgetType === 'grid' ? "use yiister\\gentelella\\widgets\\grid\\GridView;" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/** @var \yii\web\View $this */
<?= !empty($generator->searchModelClass) ? "/** @var \\" . ltrim($generator->searchModelClass, '\\') . " \$searchModel */\n" : '' ?>
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
    (function ($) {
        "use strict";

        const $grid = $("#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid");
        const $delBtn = $("#del-multi-btn");

        $grid.on("change", "input[name^=selection]", () => {
            let keys = $grid.yiiGridView("getSelectedRows");

            if (keys.length > 0) {
                $delBtn
                    .removeAttr("disabled")
                    .attr("href", "' . Url::to(['delete-multiple']) . '?ids=" + JSON.stringify(keys));
            } else {
                $delBtn.attr("disabled", "disabled");
            }
        });
    })(jQuery);
', $this::POS_END);
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
<?= $generator->enablePjax ? "    <?php Pjax::begin() ?>\n" : '' ?>
<?php if(!empty($generator->searchModelClass)): ?>

<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]) ?>
<?php endif ?>

    <p>
        <?= "<?= " ?>Html::a(\Yii::t('dashboard', 'dobavit zapis'), ['create'], [
            'class' => 'btn btn-success js_show_progress'
        ]) ?>
        <?= "<?= " ?>Html::a(\Yii::t('dashboard', 'udalit otmechennie'), ['delete-multiple'], [
            'id' => 'del-multi-btn',
            'class' => 'btn btn-danger',
            'disabled' => 'disabled',
            'data' => [
                'confirm' => \Yii::t('common', 'Udalit zapisi?'),
                'method' => 'post',
                'pjax' => 0
            ]
        ]) ?>
    </p>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'id' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-grid',
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n" : "" ?>
        'condensed' => true,
        'columns' => [
            ['class' => CheckboxColumn::class],
            ['class' => SerialColumn::class],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

            [
                'class' => ActionColumn::class,
                'viewOptions' => ['class' => 'js_show_progress'],
                'updateOptions' => ['class' => 'js_show_progress']
            ]
        ]
    ]) ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        }
    ]) ?>
<?php endif ?>
<?= $generator->enablePjax ? "    <?php Pjax::end() ?>\n" : '' ?>
</div>
