<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.12.15
 * Time: 16:18
 */

namespace dashboard\models\option;

use Matomo\Ini\IniReader;
use Matomo\Ini\IniReadingException;
use Matomo\Ini\IniWriter;
use Matomo\Ini\IniWritingException;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Read/write configuration to INI file.
 *
 * @package dashboard\models\option
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 * @link https://github.com/matomo-org/component-ini
 */
class IniConfig extends Model
{
    /**
     * @var string Path to ini file
     */
    public $path;
    /**
     * @var string Section name in the ini file
     */
    public $section;

    /**
     * @var array List of app settings
     */
    protected $array;
    /**
     * @var array List of app settings before changing
     */
    protected $old_array;

    /**
     * Read ini file.
     * @throws InvalidConfigException
     * @throws IniReadingException
     * @throws InvalidArgumentException
     * @throws InvalidRouteException
     */
    public function init(): void
    {
        if (empty($this->path) || empty($this->section)) {
            throw new InvalidConfigException('Path and section required.');
        }

        $path = \Yii::getAlias($this->path);
        if (!file_exists($path)) {
            throw new InvalidRouteException("File $path not found.");
        }

        $reader = new IniReader();
        $this->array = $reader->readFile($path);
        $this->old_array = $this->array;
    }

    /**
     * Get all options.
     * @return array
     */
    public function getAll(): array
    {
        return $this->array[$this->section];
    }

    /**
     * Get option.
     * @param string $key
     * @param mixed $default
     * @param bool $current Actual or old data
     * @return null|string Option value
     */
    public function get(string $key, $default = null, bool $current = true): ?string
    {
        $array = $current ? $this->array : $this->old_array;

        return ArrayHelper::getValue($array[$this->section], $key, $default);
    }

    /**
     * {@inheritdoc}
     * @see get()
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set option.
     * @param string $key
     * @param string|int|bool $value
     */
    public function set(string $key, $value): void
    {
        $array[$this->section] = [
            $key => $value
        ];
        $this->array = ArrayHelper::merge($this->array, $array);
    }

    /**
     * {@inheritdoc}
     * @see set()
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Save options to ini file.
     * @return bool
     * @throws InvalidArgumentException
     */
    public function save(): bool
    {
        // invalidate global cache
        $cache = \Yii::$app->getCache();
        if ($cache !== null) {
            $cache->flush();
        }

        $writer = new IniWriter();
        try {
            $writer->writeToFile(\Yii::getAlias($this->path), $this->array);
        } catch (IniWritingException $e) {
            $this->addError(__CLASS__, $e->getMessage());

            return false;
        }

        return true;
    }

}
