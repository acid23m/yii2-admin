<?php

use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<br>

<p><?= \Yii::t('dashboard', 'nuzhno pereindeksirovat') ?></p>

<a href="<?= Url::to(['search-index']) ?>" class="btn btn-block btn-success js_show_progress">
    <?= \Yii::t('dashboard', 'obnovit indeks') ?>
</a>
