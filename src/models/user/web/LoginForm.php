<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 29.07.18
 * Time: 21:01
 */

namespace dashboard\models\user\web;

use dashboard\models\user\UserIdentity;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\helpers\Html;
use yii\swiftmailer\Mailer;

/**
 * Class LoginForm.
 *
 * @package dashboard\models\user\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class LoginForm extends Model
{
    protected const CODE_CACHE_TIME = 60;
    protected const REMEMBER_ME_TIME = 2592000; // 1 month

    /**
     * @var string Username
     */
    public $username;
    /**
     * @var string Password
     */
    public $password;
    /**
     * @var bool Enable cookie auth or not
     */
    public $rememberMe = false;

    /**
     * @var null|bool|UserIdentity User model instance
     */
    private $user = false;

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'username' => \Yii::t('dashboard', 'imya polzovatelya'),
            'password' => \Yii::t('dashboard', 'parol'),
            'rememberMe' => \Yii::t('dashboard', 'zapomnit menya')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['username', 'password'], 'trim'],
            [['username', 'password'], 'required'],
            [['username'], 'string', 'min' => 2, 'max' => STRING_LENGTH_SHORT],
            [['password'], 'string', 'min' => 5, 'max' => STRING_LENGTH_SHORT],
            [['password'], 'validatePassword'],
            [['rememberMe'], 'boolean']
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     * @throws InvalidArgumentException
     */
    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, \Yii::t('dashboard', 'neverniy polzovatel ili parol'));
            }
        }
    }

    /**
     * Finds user by [[username]].
     * @return UserIdentity|null
     */
    public function getUser(): ?UserIdentity
    {
        if ($this->user === false) {
            $this->user = UserIdentity::findByUsername($this->username);
        }

        return $this->user;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login(): bool
    {
        return \Yii::$app->getUser()->login(
            $this->getUser(),
            $this->rememberMe ? self::REMEMBER_ME_TIME : 1
        );
    }

    /**
     * Send code to user.
     * @return string
     * @throws Exception
     */
    public function send(): string
    {
        $user = $this->getUser();
        $code = \Yii::$app->getSecurity()->generateRandomString(8);

        $cache_id = \Yii::$app->getSecurity()->generateRandomString(10);
        \Yii::$app->getCache()->set($cache_id, ['code' => hash('md4', $code), 'loginForm' => $this], self::CODE_CACHE_TIME);

        if ($user !== null) {
            /** @var Mailer $mailer */
            $mailer = \Yii::$app->mailer;
            $mailer->viewPath = '@vendor/acid23m/yii2-admin/src/mail';
            $mailer->htmlLayout = '@vendor/acid23m/yii2-admin/src/mail/layouts/html';
            $mailer->textLayout = '@vendor/acid23m/yii2-admin/src/mail/layouts/text';

            try {
                $mailer
                    ->compose(
                        ['html' => 'tfaCode-html', 'text' => 'tfaCode-text'],
                        compact('user', 'code')
                    )
//                    ->setFrom([\Yii::$app->get('option')->get('mail_gate_login') => \Yii::$app->name])
                    ->setFrom(['noreply@site.com' => \Yii::$app->name])
                    ->setTo($user->email)
                    ->setSubject(\Yii::t('dashboard', 'odnorazoviy parol') . ' - ' . \Yii::$app->name)
                    ->send();
            } catch (\Throwable $e) {
                \Yii::error($e->getMessage());
            }
        }

        return $cache_id;
    }

}
