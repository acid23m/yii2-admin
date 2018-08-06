<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 07.08.18
 * Time: 1:09
 */

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/** @var \yii\web\View $this */
?>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>
                    <i class="fa fa-desktop"></i>
                    <?= \Yii::t('dashboard', 'tehnicheskie dannie') ?>
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li>
                        <a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php if ($this->beginCache('server_info', ['duration' => 60])): ?>
                    <ul class="list-unstyled">
                        <li>
                            <strong>IP</strong>:
                            <?= \Yii::$app->getCache()->getOrSet('external_server_ip', function () {
                                $ch = curl_init('https://api.ipify.org');
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                                $external_ip = curl_exec($ch);
                                curl_close($ch);
                                unset($ch);
                                if ($external_ip) {
                                    return $external_ip;
                                }

                                return null;
                            }, 3600) ?>
                        </li>
                        <li>
                            <strong><?= \Yii::t('dashboard', 'host') ?></strong>:
                            <?= \Yii::$app->getRequest()->getHostName() ?>
                        </li>
                        <li>
                            <strong><?= \Yii::t('dashboard', 'os') ?></strong>:
                            <?= PHP_OS ?>
                        </li>
                        <li>
                            <strong><?= \Yii::t('dashboard', 'server') ?></strong>:
                            <?= $_SERVER['SERVER_SOFTWARE'] . ' ' . PHP_SAPI ?>
                        </li>
                        <li>
                            <strong>PHP</strong>:
                            <?= PHP_VERSION ?>
                        </li>
                        <li>
                            <?php
                            \Yii::$app->getDb()->open();
                            $dbDriverName = \Yii::$app->getDb()->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
                            echo Html::tag('strong', strtoupper($dbDriverName) . ': ');
                            echo \Yii::$app->getDb()->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
                            ?>
                        </li>
                        <?php
                        /** @var \yii\db\Connection $sqlite */
                        $sqlite = \Yii::$app->get(\dashboard\Module::DB_USER);
                        $sqlite->open();
                        $dbappDriverName = $sqlite->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

                        if ($dbDriverName !== $dbappDriverName) {
                            echo Html::beginTag('li');
                            echo Html::tag('strong', strtoupper($dbappDriverName) . ': ');
                            echo $sqlite->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
                            echo Html::endTag('li');
                        }
                        ?>
                        <li>
                            <strong>REDIS:</strong>
                            <?php
                            /** @var \yii\redis\Connection $redis */
                            $redis = \Yii::$app->get('cache')->redis;
                            $info = explode(
                                PHP_EOL,
                                $redis->info('server')
                            );
                            foreach ($info as &$row) {
                                if (strpos($row, 'redis_version') === 0) {
                                    echo str_replace('redis_version:', '', $row);
                                    break;
                                }
                            }
                            unset($row, $info);
                            ?>
                        </li>
                        <li>
                            <strong>Yii Framework</strong>:
                            <?= \Yii::getVersion() ?>
                        </li>
                    </ul>

                    <hr>

                    <?php
                    $cpu_model = '';
                    $cpu_count = 0;

                    $cpu = file('/proc/cpuinfo');

                    if ($cpu !== false) {
                        foreach ($cpu as &$info) {
                            if (StringHelper::startsWith($info, 'model name') && empty($cpu_model)) {
                                $info = trim($info);
                                $info = str_replace(["\n", "\t"], '', $info);
                                $info = ltrim($info, 'model name');
                                $cpu_model = $info;
                            }

                            if (StringHelper::startsWith($info, 'processor')) {
                                $cpu_count ++;
                            }
                        }
                        unset($info, $cpu);
                    }
                    ?>

                    <ul class="list-unstyled">
                        <li>
                            <strong><?= \Yii::t('dashboard', 'processor') ?></strong>
                            <?= $cpu_model ?>
                        </li>
                        <li>
                            <strong><?= \Yii::t('dashboard', 'yadra proc') ?></strong>:
                            <?= $cpu_count ?>
                        </li>
                    </ul>

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <?php
                            $mem_total = 0;
                            $mem_free = 0;
                            $mem_used = 0;

                            $memory = file('/proc/meminfo');
                            if ($memory !== false) {
                                foreach ($memory as &$info) {
                                    if (StringHelper::startsWith($info, 'MemTotal')) {
                                        [$key, $val] = explode(':', $info);
                                        $mem_total = (int) trim($val) * 1024;
                                    }

                                    if (StringHelper::startsWith($info, 'MemAvailable')) {
                                        [$key, $val] = explode(':', $info);
                                        $mem_free = (int) trim($val) * 1024;
                                    }
                                }
                                unset($info, $memory);

                                $mem_used = $mem_total - $mem_free;
                            }

                            $mem_total_percent = 100;
                            $mem_free_percent = ceil($mem_total_percent * $mem_free / $mem_total);
                            $mem_used_percent = $mem_total_percent - $mem_free_percent;
                            ?>

                            <strong><?= \Yii::t('dashboard', 'pamyat') ?></strong>:<br>

                            <ul class="list-unstyled">
                                <li>
                                    <?= \Yii::t('dashboard', 'svobodno') ?>:
                                    <?= \Yii::$app->getFormatter()->asShortSize($mem_free) ?>
                                    <div class="progress progress-micro">
                                        <div class="progress-bar bg-color-greenLight" style="width:<?= $mem_free_percent ?>%;"></div>
                                    </div>
                                </li>
                                <li>
                                    <?= \Yii::t('dashboard', 'zanyato') ?>:
                                    <?= \Yii::$app->getFormatter()->asShortSize($mem_used) ?>
                                    <div class="progress progress-micro">
                                        <div class="progress-bar bg-color-redLight" style="width:<?= $mem_used_percent ?>%;"></div>
                                    </div>
                                </li>
                                <li>
                                    <?= \Yii::t('dashboard', 'vsego') ?>:
                                    <?= \Yii::$app->getFormatter()->asShortSize($mem_total) ?>
                                </li>
                            </ul>
                        </div>

                        <div class="col-xs-12 col-md-6">
                            <?php
                            $disk_total = 0;
                            $disk_free = 0;
                            $disk_used = 0;

                            $ds = disk_total_space('/');
                            if ($ds !== false) {
                                $disk_total = $ds;
                            }

                            $df = disk_free_space('/');
                            if ($df !== false) {
                                $disk_free = $df;
                            }

                            $disk_used = $disk_total - $disk_free;

                            $disk_total_percent = 100;
                            $disk_free_percent = ceil($disk_total_percent * $disk_free / $disk_total);
                            $disk_used_percent = $disk_total_percent - $disk_free_percent;
                            ?>

                            <strong><?= \Yii::t('dashboard', 'disk') ?></strong>:<br>

                            <ul class="list-unstyled">
                                <li>
                                    <?= \Yii::t('dashboard', 'svobodno') ?>:
                                    <?= \Yii::$app->getFormatter()->asShortSize($disk_free) ?>
                                    <div class="progress progress-micro">
                                        <div class="progress-bar bg-color-greenLight" style="width:<?= $disk_free_percent ?>%;"></div>
                                    </div>
                                </li>
                                <li>
                                    <?= \Yii::t('dashboard', 'zanyato') ?>:
                                    <?= \Yii::$app->getFormatter()->asShortSize($disk_used) ?>
                                    <div class="progress progress-micro">
                                        <div class="progress-bar bg-color-redLight" style="width:<?= $disk_used_percent ?>%;"></div>
                                    </div>
                                </li>
                                <li>
                                    <?= \Yii::t('dashboard', 'vsego') ?>:
                                    <?= \Yii::$app->getFormatter()->asShortSize($disk_total) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php $this->endCache() ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
