<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:45
 */

use dashboard\widgets\HomePanel;

/** @var \yii\web\View $this */
/** @var bool $search_index_is_active */
/** @var \dashboard\models\log\LogSearch $logSearchModel */
/** @var \yii\data\ActiveDataProvider $logDataProvider */
/** @var \dashboard\models\user\UserIdentity $user */
$user = \Yii::$app->getUser()->getIdentity();

$this->title = \Yii::t('dashboard', 'panel');
$this->params['title'] = $this->title;
?>

<?= HomePanel::widget(['y_position' => 'top', 'x_position' => 'wide']) ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <?= HomePanel::widget(['y_position' => 'top', 'x_position' => 'left']) ?>

        <!-- User -->
        <?= $this->render('_user', \compact('user')) ?>
        <!-- /User -->

        <!-- Server -->
        <?php if (\Yii::$app->getUser()->can($user::ROLE_ADMIN)): ?>
            <?= $this->render('_server') ?>
        <?php endif ?>
        <!-- /Server -->

        <?= HomePanel::widget(['y_position' => 'bottom', 'x_position' => 'left']) ?>
    </div>

    <div class="col-xs-12 col-md-6">
        <?= HomePanel::widget(['y_position' => 'top', 'x_position' => 'right']) ?>

        <!-- Notes -->
        <?= $this->render('_user_note', \compact('user')) ?>
        <!-- /Notes -->

        <!-- Service -->
        <?php if (\Yii::$app->getUser()->can($user::ROLE_ADMIN)): ?>
            <?= $this->render('_service', \compact('user', 'search_index_is_active')) ?>
        <?php endif ?>
        <!-- /Service -->

        <?= HomePanel::widget(['y_position' => 'bottom', 'x_position' => 'right']) ?>
    </div>
</div>

<?= HomePanel::widget(['y_position' => 'bottom', 'x_position' => 'wide']) ?>

<!-- Log -->
<?php if (\Yii::$app->getUser()->can($user::ROLE_SUPER)): ?>
    <?= $this->render('_log', \compact('logSearchModel', 'logDataProvider')) ?>
<?php endif ?>
<!-- /Log -->
