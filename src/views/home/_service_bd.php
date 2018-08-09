<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 10.08.18
 * Time: 3:42
 */

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */

$db_creds = '';
if (\Yii::$app->getUser()->can($user::ROLE_SUPER)) {
    $items = [];
    $dotenv = new Dotenv\Dotenv(\Yii::getAlias('@root'));
    $dotenv->load();

    \Yii::$app->getDb()->open();
    $items[] = 'Driver: ' . \Yii::$app->getDb()->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    $items[] = 'Server: db:5432';
    $items[] = 'User name: ' . \getenv('DB_USER');
    $items[] = 'Password: ' . \getenv('DB_PASSWORD');
    $items[] = 'DB: ' . \getenv('DB_NAME_' . \strtoupper(\YII_ENV));

    $db_creds = Html::ul($items);
}
?>

<br>

<p><?= \Yii::t('dashboard', 'ostorozhno s bazoy') ?></p>

<?= $db_creds ?>

<a href="<?= Url::to('/backend/web/adminer/index.php') ?>" class="btn btn-block btn-success" target="_blank">
    <?= \Yii::t('dashboard', 'redaktor bazy') ?>
</a>
