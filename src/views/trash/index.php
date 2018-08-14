<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 14.08.18
 * Time: 2:36
 */

use dashboard\models\trash\TrashableInterface;
use yii\db\ActiveRecord;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yiister\gentelella\widgets\grid\GridView;

/** @var \yii\web\View $this */
/** @var \yii\data\ArrayDataProvider $data_provider */

$this->title = \Yii::t('dashboard', 'korzina');
$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="trash-index">
    <div class="row">
        <div class="col-xs-12">

            <p>
                <?= Html::a(\Yii::t('dashboard', 'udalit vse'), ['delete-multiple'], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'method' => 'post'
                    ]
                ]) ?>
            </p>

            <?= GridView::widget([
                'id' => 'trash-grid',
                'dataProvider' => $data_provider,
                'condensed' => true,
                'columns' => [
                    ['class' => SerialColumn::class],

                    [
                        'attribute' => 'group',
                        'label' => \Yii::t('dashboard', 'gruppa'),
                        'value' => function ($model, $key, $index) {
                            /** @var TrashableInterface $m */
                            $m = $model['model'];

                            return $m->getOwnerLabel();
                        }
                    ],
                    [
                        'attribute' => 'label',
                        'label' => \Yii::t('dashboard', 'element'),
                        'value' => function ($model, $key, $index) {
                            /** @var TrashableInterface $m */
                            $m = $model['model'];

                            return $m->getItemLabel();
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'label' => \Yii::t('dashboard', 'vremya obnovleniya'),
                        'format' => 'datetime'
                    ],

                    [
                        'class' => ActionColumn::class,
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                /** @var TrashableInterface $m */
                                $m = $model['model'];

                                return Html::a(
                                    '<span class="glyphicon glyphicon-eye-open"></span>',
                                    $m->getViewUrl(),
                                    ['title' => \Yii::t('yii', 'View')]
                                );
                            },
                            'restore' => function ($url, $model, $key) {
                                /** @var TrashableInterface|ActiveRecord $m */
                                $m = $model['model'];

                                return Html::a(
                                    '<span class="glyphicon glyphicon-repeat"></span>',
                                    [
                                        'restore',
                                        'class' => StringHelper::base64UrlEncode(\get_class($m)),
                                        'id' => StringHelper::base64UrlEncode(\serialize($m->getPrimaryKey(true)))
                                    ],
                                    ['title' => \Yii::t('dashboard', 'vosstanovit')]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                /** @var TrashableInterface|ActiveRecord $m */
                                $m = $model['model'];

                                return Html::a(
                                    '<span class="glyphicon glyphicon-remove"></span>',
                                    [
                                        'delete',
                                        'class' => StringHelper::base64UrlEncode(\get_class($m)),
                                        'id' => StringHelper::base64UrlEncode(\serialize($m->getPrimaryKey(true)))
                                    ],
                                    [
                                        'title' => \Yii::t('yii', 'Delete'),
                                        'data' => [
                                            'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'method' => 'post'
                                        ]
                                    ]
                                );
                            }
                        ],
                        'template' => '{view} {restore} {delete}',
                        'buttonOptions' => ['class' => 'js_show_progress'],
                        'contentOptions' => ['style' => 'text-align: center; vertical-align: middle;']
                    ]
                ]
            ]) ?>

        </div>
    </div>
</div>
