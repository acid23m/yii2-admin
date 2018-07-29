<?php

namespace dashboard\models\user;

use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Checks if authorID matches user passed via params.
 *
 * @package dashboard\models\user
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class AuthorRule extends Rule
{
    public $name = 'isOwner';

    /**
     * @param string|integer $userId the user ID.
     * @param Item $item the role or permission that this rule is associated width
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($userId, $item, $params): bool
    {
        return isset($params['id']) && (int) $userId === (int) $params['id'];
    }

}
