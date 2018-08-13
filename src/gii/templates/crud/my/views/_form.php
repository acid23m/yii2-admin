<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var yii\gii\generators\crud\Generator $generator */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \<?= ltrim($generator->modelClass, '\\') ?> $model */
/** @var ActiveForm $form */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">
    <div class="row">
        <div class="col-xs-12">

            <div class="panel panel-default">
                <?= "<?php " ?>$form = ActiveForm::begin() ?>

                <div class="panel-body">
                    <?= "<?php " ?>$form->errorSummary($model) ?>


<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "        <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
                </div>

                <div class="panel-footer">
                    <?= "<?= " ?>Html::submitButton($model->isNewRecord
                        ? \Yii::t('dashboard', 'sozdat')
                        : \Yii::t('dashboard', 'sohranit'), [
                            'class' => $model->isNewRecord
                                ? 'btn btn-success js_show_progress'
                                : 'btn btn-primary js_show_progress'
                        ]
                    ) ?>
                </div>

                <?= "<?php " ?>ActiveForm::end() ?>
            </div>

        </div>
    </div>
</div>
