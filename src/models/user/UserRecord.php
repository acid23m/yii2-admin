<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 25.07.18
 * Time: 0:08
 */

namespace dashboard\models\user;

use dashboard\traits\DateTime;
use imagetool\helpers\File;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\rbac\ManagerInterface;

/**
 * Class UserRecord.
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $access_token
 * @property string $auth_key
 * @property string $password_reset_token
 * @property string $role
 * @property string $ip
 * @property string $note
 * @property string $avatar
 * @property bool $tfa
 * @property bool $status
 * @property string $last_access
 * @property string $created_at
 * @property string $updated_at
 *
 * @method void touch(string $attribute) Updates a timestamp attribute to the current timestamp
 *
 * @package dashboard\models\user
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class UserRecord extends ActiveRecord
{
    use DateTime;

    public const STATUS_ACTIVE = 1;
    public const STATUS_NOT_ACTIVE = 0;

    public const TFA_ENABLED = 1;
    public const TFA_DISABLED = 0;

    public const AVATAR_WIDTH = 128;
    public const AVATAR_HEIGHT = 128;

    public const ROLE_AUTHOR = 'author';
    public const ROLE_MODER = 'moderator';
    public const ROLE_ADMIN = 'administrator';
    public const ROLE_SUPER = 'root';
    public const ROLE_DEMO = 'demonstration';

    /**
     * @var string
     */
    public $password;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return \Yii::$app->get(\dashboard\Module::DB_USER);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => \call_user_func([$this, 'getNowUTC'])
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username'], 'trim'],
            [['username'], 'required'],
            [['username'], 'unique'],
            [['username'], 'string', 'min' => 2, 'max' => STRING_LENGTH_SHORT],
            [
                ['username'],
                'match',
                'pattern' => '/^([a-zA-Z0-9_])+$/',
                'message' => \Yii::t('dashboard', 'imya polzovatelya moget sostoyat')
            ],
            [['email'], 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [
                ['password'],
                'required',
                'when' => static function (UserRecord $model, $attribute) {
                    return $model->getIsNewRecord();
                },
                'enableClientValidation' => false
            ],
            [['password'], 'string', 'min' => 5, 'max' => STRING_LENGTH_SHORT],
            [
                ['password'],
                'match',
                'pattern' => '/^([a-zA-Z0-9_~!\@\#\$\%\^\&\*\(\)])+$/',
                'message' => \Yii::t('dashboard', 'parol moget sostoyat')
            ],
            [['avatar'], 'string', 'max' => TEXT_LENGTH_NORMAL],
            [['note'], 'string', 'max' => TEXT_LENGTH_NORMAL],
            [
                ['note'],
                'filter',
                'filter' => static function ($value) {
                    return HtmlPurifier::process($value);
                }
            ],
            [['role'], 'in', 'range' => self::getAllRoles()],
            [['tfa', 'status'], 'boolean'],
            [['access_token', 'ip', 'last_access'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find(): UserQuery
    {
        return new UserQuery(static::class);
    }

    /**
     * All user roles as indexed list.
     * @return array
     */
    public static function getAllRoles(): array
    {
        $module = \dashboard\Module::getInstance();

        $list = [
            self::ROLE_DEMO,
            self::ROLE_AUTHOR,
            self::ROLE_MODER,
            self::ROLE_ADMIN,
            self::ROLE_SUPER
        ];

        if ($module !== null && ArrayHelper::isAssociative($module->user_roles)) {
            $list = ArrayHelper::merge(\array_keys($module->user_roles), $list);
        }

        return $list;
    }

    /**
     * Generates password hash from password and sets it to the model.
     * @param string $password
     * @return string Password hash
     * @throws Exception
     */
    public function generatePassword(string $password): string
    {
        return \Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Validate password.
     * @param string $password Password to validate
     * @return bool If password provided is valid for current user
     * @throws InvalidArgumentException
     */
    public function validatePassword(string $password): bool
    {
        return \Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generate new password reset token.
     * @throws Exception
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = \Yii::$app->getSecurity()->generateRandomString() . '_' . time();
    }

    /**
     * Finds out if password reset token is valid.
     * @static
     * @param null|string $token Password reset token
     * @return bool
     */
    public static function validatePasswordResetToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) \substr($token, \strrpos($token, '_') + 1);
        $expire = \dashboard\Module::getInstance()->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * Remove password reset token.
     */
    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates "remember me" authentication key.
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = \Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * Generates access token.
     * @throws Exception
     */
    public function generateAccessToken(): void
    {
        $this->access_token = \Yii::$app->getSecurity()->generateRandomString();
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $this->generateAuthKey();

            if ($insert) {
                $this->generateAccessToken();

                if (empty($this->role)) {
                    $this->role = self::ROLE_ADMIN;
                }
            }

            if (!empty($this->password)) {
                $this->password_hash = $this->generatePassword($this->password);
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     * @throws InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes): void
    {
        if ($insert || isset($changedAttributes['role'])) {
            /** @var ManagerInterface $auth_manager */
            $auth_manager = (\Yii::$app instanceof \yii\console\Application)
                ? \Yii::$app->get('authManagerBackend')
                : \Yii::$app->getAuthManager();
            $auth_manager->assign($auth_manager->getRole($this->role), $this->id);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * {@inheritdoc}
     * @return bool
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            /** @var ManagerInterface $auth_manager */
            $auth_manager = (\Yii::$app instanceof \yii\console\Application)
                ? \Yii::$app->get('authManagerBackend')
                : \Yii::$app->getAuthManager();

            $auth_manager->revoke($auth_manager->getRole($this->role), $this->id);

            // delete avatar
            $avatar_path = File::getPath($this->avatar);
            try {
                unlink($avatar_path);
            } catch (\Throwable $e) {
            }

            return true;
        }

        return false;
    }

}
