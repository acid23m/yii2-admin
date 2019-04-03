<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var yii\gii\generators\crud\Generator $generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\SerialColumn;
use kartik\sortable\Sortable;
use <?= $generator->indexWidgetType === 'grid' ? "yiister\\gentelella\\widgets\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/** @var \yii\web\View $this */
<?= !empty($generator->searchModelClass) ? "/** @var \\" . ltrim($generator->searchModelClass, '\\') . " \$searchModel */\n" : '' ?>
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var array $sortItems */

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
    <div class="row">
        <div class="col-xs-12">
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
                        'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
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
                        'buttonOptions' => ['class' => 'js_show_progress'],
                        'contentOptions' => ['style' => 'width: 90px; text-align: center; vertical-align: middle;']
                    ]
                ]
            ]) ?>
<?php else: ?>
            <?= "<?= " ?>ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => static function ($model, $key, $index, $widget) {
                    return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
                }
            ]) ?>
<?php endif ?>

        </div>
    </div>
<?= $generator->enablePjax ? "    <?php Pjax::end() ?>\n" : '' ?>
</div>

<!--<br>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-sort">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= "<?= " ?>\Yii::t('dashboard', 'sortirovka') ?></h3>
                </div>

                <div class="panel-body">
                    <?= "<?php\n" ?>
                    $this->registerCss(<<<'CSS'
.sortable-placeholder,
.sortable-dragging,
.sortable.grid li {
    width: 150px;
    height: 130px;
}
.sortable.grid li {
    float: left;
    overflow: hidden;
    text-align: left;
}
CSS
                    );

                    $this->registerJs('let sortData = new Array();', $this::POS_BEGIN);
                    ?>

                    <?= "<?php\n" ?>
                    $items = [];

                    foreach ($sortItems as $pos => $item) {
                        $content = '<figure class="model-item" data-id="' . $item->id . '">';
                        $content .= \imagetool\helpers\Html::img($item->image, ['class' => 'img-responsive']);
                        $content .= '<figcaption>' . $item->title . '</figcaption>';
                        $content .= '</figure>';

                        $items[] = compact('content');
                    }
                    ?>

                    <?= "<?= " ?>Sortable::widget([
                        'type' => Sortable::TYPE_GRID,
                        'items' => $items,
                        'options' => ['style' => 'border:none; margin: 0;'],
                        'pluginEvents' => [
                            'sortupdate' => 'function (e, obj) {
                                sortData = [];
                                jQuery(".sortable.grid li").each(function () {
                                    let $item = jQuery(this).find(".model-item"),
                                        id = $item.data("id");

                                    sortData.push(id);
                                });
                            }'
                        ]
                    ]) ?>
                    <div class="clearfix"></div>
                </div>

                <div class="panel-footer">
                    <?= "<?php " ?>$this->registerJs('
                    (function ($) {
                        "use strict";

                        $("button[name=save-order]").on("click", e => {
                            e.preventDefault();

                            if (sortData.length !== 0) {
                                window.location = "' . Url::to(['save-order']) . '?ids=" + JSON.stringify(sortData);
                            } else {
                                $("#loading-block").hide();
                            }
                        });
                    })(jQuery);
                    ', $this::POS_END) ?>

                    <?= "<?= " ?>Html::button(\Yii::t('dashboard', 'sohranit'), [
                        'class' => 'btn btn-primary js_show_progress',
                        'name' => 'save-order'
                    ]) ?>
                </div>
            </div>

        </div>
    </div>
</div>-->
