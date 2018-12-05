<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.08.18
 * Time: 14:08
 */

namespace dashboard\models\index;

use Ds\Vector;
use S2\Rose\Entity\Query;
use S2\Rose\Entity\ResultItem;
use S2\Rose\Exception\ImmutableException;
use S2\Rose\Finder;
use S2\Rose\Indexer;
use S2\Rose\Stemmer\PorterStemmerRussian;
use S2\Rose\Storage\Database\PdoStorage;
use yii\base\Component;

/**
 * Class SearchIndex.
 *
 * @package dashboard\models\index
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 * @link https://github.com/parpalak/rose
 */
class SearchIndex extends Component
{
    /**
     * @var bool Component is ready or not.
     */
    public $is_active = false;

    /**
     * @var \PDO|null
     */
    protected $connection;
    /**
     * @var Indexer
     */
    protected $indexer;
    /**
     * @var Finder
     */
    protected $finder;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $dotenv = new \Dotenv\Dotenv(
            \Yii::getAlias('@root')
        );
        $dotenv->load();

        $db_name = \getenv('S_DB_NAME');
        $db_user = \getenv('S_DB_USER');
        $db_password = \getenv('S_DB_PASSWORD');

        try {
            $pdo = new \PDO("mysql:host=search;dbname=$db_name;charset=utf8", $db_user, $db_password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->connection = $pdo;
        } catch (\PDOException $e) {
        }

        $this->is_active = $this->connection instanceof \PDO;
    }

    /**
     * @return PdoStorage|null
     */
    public function getStorage(): ?PdoStorage
    {
        return $this->is_active ? new PdoStorage($this->connection, '') : null;
    }

    /**
     * @return Indexer|null
     * @link https://github.com/parpalak/rose/blob/master/src/S2/Rose/Indexer.php
     */
    public function getIndexer(): ?Indexer
    {
        if (!$this->is_active) {
            return null;
        }

        if ($this->indexer === null) {
            $this->indexer = new Indexer(
                $this->getStorage(),
                new PorterStemmerRussian
            );
        }

        return $this->indexer;
    }

    /**
     * @return Finder|null
     * @link https://github.com/parpalak/rose/blob/master/src/S2/Rose/Finder.php
     */
    public function getFinder(): ?Finder
    {
        if (!$this->is_active) {
            return null;
        }

        if ($this->finder === null) {
            $this->finder = new Finder(
                $this->getStorage(),
                new PorterStemmerRussian
            );
        }

        return $this->finder;
    }

    /**
     * Add items to index.
     */
    public function index(): void
    {
        // check module instance
        $module = \dashboard\Module::getInstance();
        if ($module === null) {
            return;
        }

        if (!$this->is_active) {
            return;
        }

        // check configuration
        $collection_config = $module->search_items;
        if (!isset($collection_config['class'])) {
            return;
        }

        $collection_class = $collection_config['class'];
        try {
            $collection = new $collection_class;
        } catch (\Throwable $e) {
            return;
        }

        // check collection type
        if (!($collection instanceof SearchCollectionInterface)) {
            return;
        }

        $collection->index($this->getIndexer());
    }

    /**
     * Find content.
     * @param string $query
     * @param int|null $limit
     * @param int $offset
     * @return iterable|Vector|ResultItem[]
     * @throws ImmutableException
     */
    public function find(string $query, ?int $limit = null, int $offset = 0)
    {
        $q = new Query($query);
        $q->setLimit($limit);
        $q->setOffset($offset);

        $results = new Vector;

        if (!$this->is_active) {
            return $results;
        }

        $query_result = $this->getFinder()->find($q);

        foreach ($query_result->getItems() as $item) {
            $results->push($item);
        }

        return $results;
    }

}
