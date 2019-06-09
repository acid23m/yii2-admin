<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
/** @var string $code */
?>

<div class="password-reset">
    <p>
        <?= \Yii::t('dashboard', 'privet') ?>,
        <strong><?= Html::encode($user->username) ?></strong>
    </p>

    <p><?= \Yii::t('dashboard', 'odnorazoviy parol') ?>:</p>

    <p><?= $code ?></p>
</div>
