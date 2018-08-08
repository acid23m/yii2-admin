<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 08.08.18
 * Time: 15:12
 */

use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<br>

<p><?= \Yii::t('dashboard', 'sbrosit kesh vruchnuyu') ?></p>

<a href="<?= Url::to(['clear-cache']) ?>" class="btn btn-block btn-success js_show_progress">
    <?= \Yii::t('dashboard', 'sbrosit kesh') ?>
</a>
