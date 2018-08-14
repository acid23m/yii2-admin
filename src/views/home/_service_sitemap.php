<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 15.08.18
 * Time: 2:09
 */

use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<br>

<p><?= \Yii::t('dashboard', 'sozdanie kart xml') ?></p>

<a href="<?= Url::to(['sitemap']) ?>" class="btn btn-block btn-success js_show_progress">
    <?= \Yii::t('dashboard', 'generirovat kartu') ?>
</a>
