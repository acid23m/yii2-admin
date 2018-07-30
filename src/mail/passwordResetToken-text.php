<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
/** @var string $resetLink */
?>

<?= \Yii::t('dashboard', 'privet') ?>, <?= Html::encode($user->username) ?>

<?= \Yii::t('dashboard', 'dlya sozdaniya parola sleduyte po ssylke') ?>:

<?= $resetLink ?>
