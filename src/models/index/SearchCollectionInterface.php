<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 24.08.18
 * Time: 0:32
 */

namespace dashboard\models\index;

use S2\Rose\Indexer;

/**
 * Collection of items for index.
 *
 * @package dashboard\models\index
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
interface SearchCollectionInterface
{
    /**
     * Add items to index.
     * @param Indexer $indexer
     * @link https://github.com/parpalak/rose#indexing
     */
    public function index(Indexer $indexer): void;
}
