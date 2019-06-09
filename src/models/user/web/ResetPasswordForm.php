<?php

namespace dashboard\models\user\web;

use dashboard\models\user\UserIdentity;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Password reset form.
 *
 * @package dashboard\models\user\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class ResetPasswordForm extends Model
{
    /**
     * @var string New password
     */
    public $password;

    /**
     * @var UserIdentity User model
     */
    private $user;

    /**
     * Creates a form model given a token.
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !\is_string($token)) {
            throw new InvalidArgumentException(\Yii::t('dashboard', 'kluch ne mozhet bit pustim'));
        }

        $this->user = UserIdentity::findByPasswordResetToken($token);

        if ($this->user === null) {
            throw new InvalidArgumentException(\Yii::t('dashboard', 'neverniy kluch sbrosa parolya'));
        }

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'password' => \Yii::t('dashboard', 'noviy parol')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['password'], 'trim'],
            [['password'], 'required'],
            [['password'], 'string', 'min' => 5, 'max' => STRING_LENGTH_SHORT],
            [
                ['password'],
                'match',
                'pattern' => '/^([a-zA-Z0-9_~!\@\#\$\%\^\&\*\(\)])+$/',
                'message' => \Yii::t('dashboard', 'parol moget sostoyat')
            ]
        ];
    }

    /**
     * Resets password.
     * @return boolean if password was reset
     * @throws Exception
     */
    public function resetPassword(): bool
    {
        $this->user->password = $this->password;
        $this->user->removePasswordResetToken();
        $this->user->generateAuthKey();

        return $this->user->save(false);
    }

}
