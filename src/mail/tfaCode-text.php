<?php

/** @var yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
/** @var string $code */
?>

<?= \Yii::t('dashboard', 'privet') ?>, <?= $user->username ?>

<?= \Yii::t('dashboard', 'odnorazoviy parol') ?>:

<?= $code ?>
