<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:45
 */

/** @var \yii\web\View $this */
/** @var \dashboard\models\log\LogSearch $logSearchModel */
/** @var \yii\data\ActiveDataProvider $logDataProvider */
/** @var \dashboard\models\user\UserIdentity $user */
$user = \Yii::$app->getUser()->getIdentity();

$this->title = \Yii::t('dashboard', 'panel');
$this->params['title'] = $this->title;
?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <!--User-->
        <?= $this->render('_user', compact('user')) ?>
        <!--/User-->
    </div>

    <div class="col-xs-12 col-md-6"></div>
</div>

<!-- Log -->
<?php if (\Yii::$app->getUser()->can($user::ROLE_SUPER)): ?>
    <?= $this->render('_log', compact('logSearchModel', 'logDataProvider')) ?>
<?php endif ?>
<!-- /Log -->
