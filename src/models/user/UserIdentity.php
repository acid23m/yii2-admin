<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 27.07.18
 * Time: 0:35
 */

namespace dashboard\models\user;

use yii\web\IdentityInterface;

/**
 * Class UserIdentity.
 *
 * @package dashboard\models\user
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class UserIdentity extends UserRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     * @return string|array|null
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Find user by ID.
     * @static
     * @param int $id
     * @return null|UserIdentity
     */
    public static function findIdentity($id): ?UserIdentity
    {
        return static::findOne($id);
    }

    /**
     * Find user by username.
     * @static
     * @param string $username
     * @return null|UserIdentity
     */
    public static function findByUsername($username): ?UserIdentity
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Find user by token.
     * @static
     * @param string $token Access token
     * @param string $type
     * @return null|UserIdentity
     */
    public static function findIdentityByAccessToken($token, $type = null): ?UserIdentity
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Find user by password reset token.
     * @static
     * @param string $token Password reset token
     * @return null|UserIdentity
     */
    public static function findByPasswordResetToken(string $token): ?UserIdentity
    {
        if (!static::validatePasswordResetToken($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE
        ]);
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

}
