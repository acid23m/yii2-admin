<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 07.08.18
 * Time: 0:08
 */

use yii\helpers\Html;
use yii\helpers\Inflector;

/** @var \yii\web\View $this */
/** @var \dashboard\models\user\UserIdentity $user */
?>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>
                    <i class="fa fa-user"></i>
                    <?= \Yii::t('dashboard', 'polzovatel') ?>
                </h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <ul class="list-unstyled">
                    <li>
                        <strong><?= \Yii::t('dashboard', 'imya polzovatelya') ?></strong>:
                        <span class="label label-primary"><?= Html::encode($user->username) ?></span>
                    </li>
                    <li>
                        <strong><?= \Yii::t('dashboard', 'rol') ?></strong>:
                        <span class="label label-primary">
                            <?= \Yii::$app->getAuthManager()->getRole($user->role)->description ?>
                        </span>
                    </li>
                    <li>
                        <strong>Email</strong>:
                        <?= $user->email ?>
                    </li>
                    <li>
                        <strong><?= \Yii::t('dashboard', 'vremya sozdaniya') ?></strong>:
                        <?= \Yii::$app->getFormatter()->asDatetime($user->created_at) ?>
                    </li>
                    <li>
                        <strong><?= \Yii::t('dashboard', 'vremya obnovleniya') ?></strong>:
                        <?= \Yii::$app->getFormatter()->asDatetime($user->updated_at) ?>
                    </li>
                    <li>
                        <strong><?= \Yii::t('dashboard', 'posledniy vhod') ?></strong>:
                        <?= \Yii::$app->getFormatter()->asDatetime($user->last_access) ?>
                    </li>
                    <li>
                        <strong>IP</strong>:
                        <?= $user->ip ?>
                    </li>
                    <li>
                        <strong><?= \Yii::t('dashboard', 'prava') ?></strong>:
                        <?php
                        $permissions = \Yii::$app->getAuthManager()->getPermissionsByUser($user->id);

                        $words = [];
                        foreach ($permissions as &$perm) {
                            $words[] = $perm->description;
                        }
                        unset($perm);

                        echo Inflector::sentence($words);
                        ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
