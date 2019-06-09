<?php

namespace dashboard\models\user;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[UserRecord]].
 *
 * @see UserRecord
 */
final class UserQuery extends ActiveQuery
{
    /**
     * Scope.<br/>
     * Adds condition that show only active users
     * @return $this
     */
    public function active(): self
    {
        $this->andWhere(['{{%user}}.[[status]]' => UserRecord::STATUS_ACTIVE]);

        return $this;
    }

    /**
     * Scope.<br/>
     * Adds condition that except superuser
     * @return $this
     */
    public function limited(): self
    {
        $this->andWhere('{{%user}}.[[role]]<>"' . UserRecord::ROLE_SUPER . '"');

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return UserRecord[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserRecord|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}