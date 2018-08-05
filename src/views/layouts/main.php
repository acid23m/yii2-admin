<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:31
 */

use dashboard\widgets\Growl;
use dashboard\widgets\LeftMenu;
use dashboard\widgets\Push;
use dashboard\widgets\TopMenu;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yiister\gentelella\widgets\Menu as GMenu;

/** @var \yii\web\View $this */
/** @var string $content */
/** @var \dashboard\controllers\web\BaseController $controller */
$controller = $this->context;
/** @var \dashboard\models\user\UserIdentity $user */
$user = \Yii::$app->getUser()->getIdentity();
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

    <base href="<?= rtrim(Url::home(true), '/') ?>">

    <title><?= Html::encode(\Yii::$app->name) ?> | <?= $this->title ?></title>

    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body class="nav-<?= !empty($_COOKIE['menuIsCollapsed']) && $_COOKIE['menuIsCollapsed'] === 'true' ? 'sm' : 'md' ?>">
<?php $this->beginBody() ?>

<div class="container body">

    <div class="main_container">

        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <div class="navbar nav_title" style="border: 0 none">
                    <a href="/" class="site_title">
<!--                        <i class="fa fa-paw"></i>-->
                        <span><?= Html::encode(\Yii::$app->name) ?></span>
                    </a>
                </div>
                <div class="clearfix"></div>

                <!-- menu prile quick info -->
                <div class="profile">
                    <div class="profile_pic">
                        <img class="img-circle profile_img" src="<?= $user->avatar ?>" alt="<?= $user->username ?>">
                    </div>
                    <div class="profile_info">
                        <span><?= \Yii::t('dashboard', 'privet') ?>,</span>
                        <h2><?= $user->username ?></h2>
                    </div>
                </div>
                <div class="clearfix"></div>
                <!-- /menu prile quick info -->

                <br>

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

                    <div class="menu_section">
                        <h3><?= \Yii::t('dashboard', 'osnovnoe') ?></h3>
                        <?= GMenu::widget([
                            'items' => [
                                [
                                    'label' => \Yii::t('dashboard', 'glavnaya'),
                                    'url' => '/',
                                    'icon' => 'home'
                                ]
                            ]
                        ]) ?>
                    </div>

                    <?= LeftMenu::widget() ?>

                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <!--<div class="sidebar-footer hidden-small">
                    <a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Logout">
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                    </a>
                </div>-->
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">

            <div class="nav_menu">
                <nav class="" role="navigation">
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <!-- users -->
                        <li>
                            <a href="#" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="<?= $user->avatar ?>" alt="<?= $user->username ?>">
                                <?= $user->username ?>
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li>
                                    <a href="<?= Url::to([
                                        "/{$controller->module->id}/user/view",
                                        'id' => $user->id
                                    ]) ?>">
                                        <i class="fa fa-user pull-right"></i>
                                        <?= \Yii::t('dashboard', 'profil') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(["/{$controller->module->id}/user/index"]) ?>">
                                        <i class="fa fa-users pull-right"></i>
                                        <?= \Yii::t('dashboard', 'polzovateli') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(["/{$controller->module->id}/user/create"]) ?>">
                                        <i class="fa fa-plus-circle pull-right"></i>
                                        <?= \Yii::t('dashboard', 'dobavit polzovatelya') ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(["/{$controller->module->id}/auth/logout"]) ?>"
                                       data-method="post"
                                       data-confirm="<?= \Yii::t('dashboard', 'vi tochno hotite viyti?') ?>">
                                        <i class="fa fa-sign-out pull-right"></i>
                                        <?= \Yii::t('dashboard', 'viyti') ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- /users -->

                        <!-- options -->
                        <li>
                            <a href="<?= Url::to(["/{$controller->module->id}/option-main/index"]) ?>">
                                <i class="fa fa-cog"></i>
                            </a>
                        </li>
                        <!-- /options -->


                        <!--<li role="presentation" class="dropdown">
                            <a href="#" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-envelope-o"></i>
                                <span class="badge bg-green">6</span>
                            </a>
                            <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                                <li>
                                    <a>
                                        <span class="image">
                                            <img src="https://placehold.it/128x128" alt="Profile Image">
                                        </span>
                                        <span>
                                            <span>John Smith</span>
                                            <span class="time">3 mins ago</span>
                                        </span>
                                        <span class="message">
                                            Film festivals used to be do-or-die moments for movie makers. They were where...
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a>
                                        <span class="image">
                                            <img src="https://placehold.it/128x128" alt="Profile Image">
                                        </span>
                                        <span>
                                            <span>John Smith</span>
                                            <span class="time">3 mins ago</span>
                                        </span>
                                        <span class="message">
                                            Film festivals used to be do-or-die moments for movie makers. They were where...
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a>
                                        <span class="image">
                                            <img src="https://placehold.it/128x128" alt="Profile Image">
                                        </span>
                                        <span>
                                            <span>John Smith</span>
                                            <span class="time">3 mins ago</span>
                                        </span>
                                        <span class="message">
                                            Film festivals used to be do-or-die moments for movie makers. They were where...
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <div class="text-center">
                                        <a href="/">
                                            <strong>See All Alerts</strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-cog"></i>
                            </a>
                            <ul class="dropdown-menu list-unstyled" role="menu">
                                <li>
                                    <a href="#">Profile</a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="badge bg-red pull-right">50%</span>
                                        <span>Settings</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a class="info-number" href="#">
                                <i class="fa fa-circle-o"></i>
                                <span class="badge bg-green">6</span>
                            </a>
                        </li>-->
                        <?= TopMenu::widget() ?>
                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
            <?= Growl::widget() ?>

            <?php if (isset($this->params['title'])): ?>
                <div class="page-title">
                    <div class="title_left">
                        <h1><?= $this->params['title'] ?></h1>
                    </div>
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">Go!</button>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <div class="clearfix"></div>

            <?= Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'] ?? [],
            ]) ?>

            <?= Push::widget() ?>

            <?= $content ?>
        </div>
        <!-- /page content -->
        <!-- footer content -->
        <footer>
            <div class="pull-right">
                <?= \Yii::t('dashboard', 'razrabotano na') ?>
                <a href="https://www.yiiframework.com" rel="nofollow" target="_blank">Yii 2 framework</a>
                &nbsp;|&nbsp;
                <?= \Yii::t('dashboard', 'razrabotchik') ?>
                <a href="<?= \dashboard\Module::getInstance()->params['author.url'] ?>" target="_blank">
                    <?= \dashboard\Module::getInstance()->params['author.name'] ?>
                </a>
            </div>
            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    </div>

</div>

<div id="custom_notifications" class="custom-notifications dsp_none">
    <ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
    </ul>
    <div class="clearfix"></div>
    <div id="notif-group" class="tabbed_notifications"></div>
</div>
<!-- /footer content -->

<?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>
