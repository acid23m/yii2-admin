<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 06.08.18
 * Time: 0:01
 */

namespace dashboard\models\log;

use dashboard\traits\Model;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\log\Logger;

/**
 * Application log.
 *
 * @property integer $id
 * @property integer $level
 * @property string $category
 * @property integer $log_time
 * @property string $prefix
 * @property string $message
 *
 * @package dashboard\models\log
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class LogRecord extends ActiveRecord
{
    use Model;

    protected $levels;

    /**
     * @inheritdoc
     */
    public static function getDb(): Connection
    {
        return \Yii::$app->get('dbRuntime');
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'log';
    }

    public function init(): void
    {
        parent::init();

        $this->levels = [
            Logger::LEVEL_ERROR => 'error',
            Logger::LEVEL_INFO => 'info',
            Logger::LEVEL_PROFILE => 'profile',
            Logger::LEVEL_PROFILE_BEGIN => 'profile begin',
            Logger::LEVEL_PROFILE_END => 'profile end',
            Logger::LEVEL_TRACE => 'trace',
            Logger::LEVEL_WARNING => 'warning'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'level' => \Yii::t('dashboard', 'uroven'),
            'category' => \Yii::t('dashboard', 'kategoria'),
            'log_time' => \Yii::t('dashboard', 'vremya'),
            'prefix' => \Yii::t('dashboard', 'prefiks'),
            'message' => \Yii::t('dashboard', 'soobshenie')
        ];
    }

}
