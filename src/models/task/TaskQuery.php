<?php

namespace dashboard\models\task;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[TaskRecord]].
 *
 * @see TaskRecord
 */
final class TaskQuery extends ActiveQuery
{
    /**
     * Show only published items.
     * @return $this
     */
    public function published(): self
    {
        $this->andWhere(['{{task}}.[[status]]' => TaskRecord::STATUS_ACTIVE]);

        return $this;
    }

    /**
     * Show only draft items.
     * @return $this
     */
    public function draft(): self
    {
        $this->andWhere(['{{task}}.[[status]]' => TaskRecord::STATUS_NOT_ACTIVE]);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return TaskRecord[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TaskRecord|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}