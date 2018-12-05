<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 18.08.18
 * Time: 23:49
 */

namespace dashboard\models\task;

use dashboard\traits\Model;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\Connection;
use yii2tech\crontab\CronJob;
use yii2tech\crontab\CronTab;

/**
 * Cron job.
 *
 * @property int $id
 * @property string $name
 * @property string $min
 * @property string $hour
 * @property string $day
 * @property string $month
 * @property string $weekDay
 * @property string $command
 * @property bool $status
 *
 * @package dashboard\models\task
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class TaskRecord extends ActiveRecord
{
    use Model;

    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * @return Connection the database connection used by this AR class.
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return \Yii::$app->get(\dashboard\Module::DB_TASK);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{task}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'min', 'hour', 'day', 'month', 'weekDay', 'command'], 'trim'],
            [['name', 'min', 'hour', 'day', 'month', 'weekDay', 'command', 'status'], 'required'],
            [['name', 'min', 'hour', 'day', 'month', 'weekDay', 'command'], 'string', 'max' => STRING_LENGTH_LONG],
            [['min', 'hour'], 'match', 'pattern' => '/^[0-9\*\/\,\-]+$/s'],
            ['month', 'match', 'pattern' => '/^[A-Z0-9\*\/\,\-]+$/s'],
            ['day', 'match', 'pattern' => '/^[0-9\*\/\,\-\?LW]+$/s'],
            ['weekDay', 'match', 'pattern' => '/^[A-Z0-6\*\/\,\-L#]+$/s'],
            [['status'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find(): TaskQuery
    {
        return new TaskQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            // first remove all tasks
            $tasks = self::find()->all();
            $jobs = [];
            foreach ($tasks as $task) {
                $job = new CronJob;
                $job->setAttributes(
                    $task->getAttributes()
                );
                $jobs[] = $job;
            }
            $tab = new CronTab;
            $tab->setJobs($jobs)->remove();

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes): void
    {
        // save new tasks
        $tasks = self::find()->published()->all();
        $jobs = [];
        foreach ($tasks as $task) {
            $job = new CronJob;
            $job->setAttributes(
                $task->getAttributes()
            );
            $jobs[] = $job;
        }
        $tab = new CronTab([
            'headLines' => [
                'SHELL=/bin/bash',
                'PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/app'
            ]
        ]);
        $tab->setJobs($jobs)->apply();

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            // set cron job
            $job = new CronJob;
            $job->setAttributes(
                $this->getAttributes()
            );

            // delete cron job
            $tab = new CronTab;
            $tab->setJobs([$job])->remove();

            return true;
        }

        return false;
    }

}
