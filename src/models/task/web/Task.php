<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 19.08.18
 * Time: 0:34
 */

namespace dashboard\models\task\web;

use dashboard\models\task\TaskRecord;

/**
 * Class Task.
 *
 * @package dashboard\models\task\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Task extends TaskRecord
{
    /**
     * @var array
     */
    protected $statuses;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->statuses = [
            self::STATUS_NOT_ACTIVE => \Yii::t('dashboard', 'chernovik'),
            self::STATUS_ACTIVE => \Yii::t('dashboard', 'opublikovana')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => \Yii::t('dashboard', 'zagolovok'),
            'min' => \Yii::t('dashboard', 'minuta'),
            'hour' => \Yii::t('dashboard', 'chas'),
            'day' => \Yii::t('dashboard', 'den'),
            'month' => \Yii::t('dashboard', 'mesac'),
            'weekDay' => \Yii::t('dashboard', 'den nedeli'),
            'command' => \Yii::t('dashboard', 'komanda'),
            'status' => \Yii::t('dashboard', 'status')
        ];
    }

}
