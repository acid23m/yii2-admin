<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 31.10.18
 * Time: 11:28
 */

namespace dashboard\models\index;

use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class SearchIndexJob.
 *
 * @package dashboard\models\index
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class SearchIndexJob implements JobInterface
{
    /**
     * @param Queue $queue
     * @throws \S2\Rose\Exception\LogicException
     * @throws \S2\Rose\Exception\UnknownException
     * @throws \S2\Rose\Storage\Exception\InvalidEnvironmentException
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        /** @var SearchIndex $search_index */
        $search_index = \Yii::createObject(SearchIndex::class);
        $search_index->getStorage()->erase(); // clear
        $search_index->index(); // index
    }

}
