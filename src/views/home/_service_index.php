<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.08.18
 * Time: 14:26
 */

use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<br>

<p><?= \Yii::t('dashboard', 'nuzhno pereindeksirovat') ?></p>

<a href="<?= Url::to(['search-index']) ?>" class="btn btn-block btn-success js_show_progress">
    <?= \Yii::t('dashboard', 'obnovit indeks') ?>
</a>
