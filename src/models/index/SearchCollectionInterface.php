<?php

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
     * Adds items to index.
     * @param Indexer $indexer
     * @link https://github.com/parpalak/rose#indexing
     */
    public function index(Indexer $indexer): void;

}
