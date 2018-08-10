<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 11.08.18
 * Time: 1:07
 */

namespace dashboard\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * PHP Secure Configuration Checker.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class PccController extends Controller
{
    /**
     * Check current PHP configuration for potential security flaws.
     * @return int
     * @throws \yii\base\InvalidArgumentException
     */
    public function actionIndex(): int
    {
        $pcc_path = \Yii::getAlias('@vendor/acid23m/yii2-admin/src/phpconfigcheck.php');
        \passthru("/usr/bin/php $pcc_path");

        return ExitCode::OK;
    }

}
