<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.08.18
 * Time: 23:31
 */

namespace dashboard\commands;

use dashboard\models\index\SearchIndex;
use S2\Rose\Exception\LogicException;
use S2\Rose\Exception\UnknownException;
use S2\Rose\Storage\Exception\InvalidEnvironmentException;
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
     * @throws LogicException
     * @throws UnknownException
     * @throws InvalidEnvironmentException
     * @throws InvalidConfigException
     */
    public function actionErase(): int
    {
        /** @var SearchIndex $search_index */
        $search_index = \Yii::createObject(SearchIndex::class);
        $search_index->getStorage()->erase();

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Add items to search index.
     * @return int
     * @throws LogicException
     * @throws UnknownException
     * @throws InvalidEnvironmentException
     * @throws InvalidConfigException
     */
    public function actionIndex(): int
    {
        /** @var SearchIndex $search_index */
        $search_index = \Yii::createObject(SearchIndex::class);
        $search_index->getStorage()->erase(); // clear
        $search_index->index(); // index

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

}
