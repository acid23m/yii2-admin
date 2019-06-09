<?php

namespace dashboard\commands;

use dashboard\models\sitemap\SitemapGenerator;
use dashboard\models\sitemap\SitemapJob;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\queue\Queue;

/**
 * Sitemap files.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class SitemapController extends Controller
{
    /**
     * Creates sitemap files.
     * @return int
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function actionIndex(): int
    {
        /** @var Queue $queue */
        $queue = \Yii::$app->get('queue', false);
        if ($queue instanceof Queue) {
            $queue->push(new SitemapJob);
        } else {
            SitemapGenerator::write();
        }

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

}
