<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.08.18
 * Time: 23:31
 */

namespace dashboard\commands;

use dashboard\models\index\SearchIndex;
use dashboard\models\index\SearchIndexJob;
use S2\Rose\Exception\LogicException;
use S2\Rose\Exception\UnknownException;
use S2\Rose\Storage\Exception\InvalidEnvironmentException;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manage search index.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class SearchController extends Controller
{
    /**
     * Recreate index DB.
     * @return int
     * @throws InvalidConfigException
     * @throws InvalidEnvironmentException
     * @throws LogicException
     * @throws UnknownException
     * @throws InvalidArgumentException
     */
    public function actionErase(): int
    {
        /** @var SearchIndex $search_index */
        $search_index = \Yii::createObject(SearchIndex::class);
        if ($search_index->is_active) {
            $search_index->getStorage()->erase();
        }

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Add items to search index.
     * @return int
     * @throws InvalidConfigException
     * @throws InvalidEnvironmentException
     * @throws LogicException
     * @throws UnknownException
     * @throws InvalidArgumentException
     */
    public function actionIndex(): int
    {
        /** @var \yii\queue\Queue $queue */
        $queue = \Yii::$app->get('queue', false);
        if ($queue instanceof \yii\queue\Queue) {
            $queue->push(new SearchIndexJob);
        } else {
            /** @var SearchIndex $search_index */
            $search_index = \Yii::createObject(SearchIndex::class);
            if ($search_index->is_active) {
                $search_index->getStorage()->erase(); // clear
                $search_index->index(); // index
            }
        }

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

}
