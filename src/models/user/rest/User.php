<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 05.08.18
 * Time: 15:43
 */

namespace dashboard\models\user\rest;

use dashboard\models\user\UserRecord;

/**
 * Class User.
 *
 * @package dashboard\models\user\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class User extends UserRecord
{
    /**
     * {@inheritdoc}
     */
    public function fields(): array
    {
        $fields = parent::fields();

        unset(
            $fields['auth_key'],
            $fields['password_hash'],
            $fields['password_reset_token']
        );

        if (
            !\Yii::$app->getUser()->can('isOwner', ['id' => $this->id])
            && !\Yii::$app->getUser()->can(self::ROLE_ADMIN)
        ) {
            unset(
                $fields['note'],
                $fields['role'],
                $fields['ip'],
                $fields['access_token'],
                $fields['last_access'],
//                $fields['created_at'],
//                $fields['updated_at'],
                $fields['status']
            );
        }

        return $fields;
    }

}
