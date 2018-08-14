<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 15.08.18
 * Time: 2:24
 */

namespace dashboard\commands;

use dashboard\models\sitemap\SitemapGenerator;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Sitemap files.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class SitemapController extends Controller
{
    /**
     * Create sitemap files.
     * @return int
     * @throws \InvalidArgumentException
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): int
    {
        SitemapGenerator::write();

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

}