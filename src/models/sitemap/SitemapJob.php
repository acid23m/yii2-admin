<?php

namespace dashboard\models\sitemap;

use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class SitemapJob.
 *
 * @package dashboard\models\sitemap
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class SitemapJob implements JobInterface
{
    /**
     * @param Queue $queue
     * @throws \InvalidArgumentException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        SitemapGenerator::write();
    }

}
