<?php

use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<br>

<p><?= \Yii::t('dashboard', 'sbrosit kesh vruchnuyu') ?></p>

<a href="<?= Url::to(['clear-cache']) ?>" class="btn btn-block btn-success js_show_progress">
    <?= \Yii::t('dashboard', 'sbrosit kesh') ?>
</a>
