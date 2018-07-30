<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
/** @var string $resetLink */
?>

<div class="password-reset">
    <p>
        <?= \Yii::t('dashboard', 'privet') ?>,
        <strong><?= Html::encode($user->username) ?></strong>
    </p>

    <p><?= \Yii::t('dashboard', 'dlya sozdaniya parola sleduyte po ssylke') ?>:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
