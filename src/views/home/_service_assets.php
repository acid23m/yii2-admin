<?php

use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<br>

<p><?= \Yii::t('dashboard', 'direktoriya s resursami') ?></p>

<a href="<?= Url::to(['clear-assets']) ?>" class="btn btn-block btn-success js_show_progress">
    <?= \Yii::t('dashboard', 'ochistit papku s resursami') ?>
</a>
