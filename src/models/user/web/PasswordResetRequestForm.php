<?php

namespace dashboard\models\user\web;

use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\helpers\Url;
use yii\swiftmailer\Mailer;

/**
 * Password reset request form.
 *
 * @package dashboard\models\user\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class PasswordResetRequestForm extends Model
{
    /**
     * @var string Email for password reset
     */
    public $email;

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email'], 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
            [
                ['email'],
                'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => \Yii::t('dashboard', 'polzovatel ne nayden')
            ]
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     * @return bool whether the email was send
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function send(): bool
    {
        /** @var null|User $user */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email
        ]);

        if ($user === null) {
            return false;
        }

        // do not create new token if current present and still valid
        if (User::validatePasswordResetToken($user->password_reset_token)) {
            return false;
        }

        $user->generatePasswordResetToken();

        if (!$user->save(false, ['password_reset_token'])) {
            return false;
        }

        $module = \dashboard\Module::getInstance();
        $resetLink = '';
        if ($module !== null) {
            $resetLink = Url::to(["/{$module->id}/auth/reset-password", 'token' => $user->password_reset_token], true);
        }

        /** @var Mailer $mailer */
        $mailer = \Yii::$app->getMailer();
        $mailer->viewPath = '@vendor/acid23m/yii2-admin/src/mail';
        $mailer->htmlLayout = '@vendor/acid23m/yii2-admin/src/mail/layouts/html';
        $mailer->textLayout = '@vendor/acid23m/yii2-admin/src/mail/layouts/text';

        try {
            return $mailer
                ->compose(
                    ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                    \compact('user', 'resetLink')
                )
                ->setFrom([\Yii::$app->get('option')->get('mail_gate_login') => \Yii::$app->name])
                ->setTo($this->email)
                ->setSubject(\Yii::t('dashboard', 'sbros parolya') . ' - ' . \Yii::$app->name)
                ->send();
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage());

            return false;
        }
    }

}
