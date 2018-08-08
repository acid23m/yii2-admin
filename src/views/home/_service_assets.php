<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 08.08.18
 * Time: 17:20
 */

use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<br>

<p><?= \Yii::t('dashboard', 'direktoriya s resursami') ?></p>

<a href="<?= Url::to(['clear-assets']) ?>" class="btn btn-block btn-success js_show_progress">
    <?= \Yii::t('dashboard', 'ochistit papku s resursami') ?>
</a>
