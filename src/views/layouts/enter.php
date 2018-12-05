<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 29.07.18
 * Time: 21:33
 */

use dashboard\assets\AppAsset;
use dashboard\assets\BootboxAsset;
use dashboard\widgets\Growl;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var string $content */

$this->title = Html::encode(\Yii::t('dashboard', 'panel'));

AppAsset::register($this);
BootboxAsset::overrideSystemConfirm();
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Security-Policy" content="block-all-mixed-content">
    <meta name="robots" content="noindex,nofollow">

    <base href="<?= \rtrim(Url::home(true), '/') ?>">

    <title><?= Html::encode(\Yii::$app->name) ?> | <?= $this->title ?></title>

    <style>
        body {
            width: 100%;
            overflow-x: hidden;
        }
    </style>

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>

<main class="container">
    <div style="margin-top: 7%;"></div>
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">

            <?= Growl::widget() ?>

            <?= $content ?>

            <div class="clearfix" role="presentation">
                <p class="pull-left">
                    <small>
                        2011&mdash;<?= \date('Y') ?> &copy;
                        <a href="<?= \dashboard\Module::getInstance()->params['author.url'] ?>" target="_blank">
                            <?= \dashboard\Module::getInstance()->params['author.name'] ?>
                        </a>
                    </small>
                </p>
                <p class="pull-right">
                    <?= Html::a('Yii', 'http://www.yiiframework.com/', [
                        'class' => 'clear-link',
                        'title' => \Yii::t('dashboard', 'razrabotano na') . ' Yii Framework',
                        'target' => '_blank'
                    ]) ?>
                </p>
            </div>
        </div>
    </div>
</main>

<?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>
