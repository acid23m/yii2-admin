<?php

namespace dashboard\commands;

use yii\base\InvalidArgumentException;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Security tools.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class SecurityController extends Controller
{
    /**
     * Checks current PHP configuration for potential security flaws.
     * @return int
     * @throws InvalidArgumentException
     */
    public function actionPcc(): int
    {
        $pcc_path = \Yii::getAlias('@vendor/acid23m/yii2-admin/src/phpconfigcheck.php');
        \passthru("PCC_OUTPUT_TYPE=text /usr/bin/php $pcc_path");

        return ExitCode::OK;
    }

    /**
     * Smart PHP vulnerability detector.
     * @return int
     * @throws InvalidArgumentException
     */
    public function actionMalwareDetector(): int
    {
        (new \Ollyxar\AntiMalware\Scanner(\Yii::getAlias('@root')))->run();

        return ExitCode::OK;
    }

}
