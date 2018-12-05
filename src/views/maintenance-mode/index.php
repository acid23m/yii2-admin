<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 16.08.18
 * Time: 21:34
 */

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
