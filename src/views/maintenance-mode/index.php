<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $title */
/** @var string $message */
?>

<h1><?= Html::encode($title) ?></h1>

<p><?= Html::encode($message) ?></p>

<hr>

<p class="creds">
    <?= \date('Y') ?> &copy; <?= \Yii::$app->get('option')->get('app_name') ?>
</p>
