<?php

namespace dashboard\models\user\web;

use yii\base\Model;

/**
 * Class TfaCodeForm.
 *
 * @package dashboard\models\user\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class TfaCodeForm extends Model
{
    /**
     * @var string 2fa code
     */
    public $code;
    /**
     * @var string
     */
    public $ci;

    /**
     * @var null|array [code, loginForm]
     */
    private $data;

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'code' => \Yii::t('dashboard', 'odnorazoviy parol'),
            'ci' => 'Cache ID'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['code', 'ci'], 'trim'],
            [['code', 'ci'], 'required'],
            [['code', 'ci'], 'string'],
            [['code'], 'validateCode']
        ];
    }

    /**
     * Validates the code.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateCode($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            if ($this->data['code'] !== \hash('md4', $this->code) || $this->data['loginForm'] === null) {
                $this->addError($attribute, \Yii::t('dashboard', 'neverniy kod'));

                return;
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login(): bool
    {
        /** @var LoginForm $login_form */
        $login_form = $this->data['loginForm'];

        if ($login_form === null) {
            return false;
        }

        $result = $login_form->login();
        \Yii::$app->getCache()->delete($this->ci);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $data = \Yii::$app->getCache()->get($this->ci);
            $this->data = $data === false ? ['code' => null, 'loginForm' => null] : $data;

            return true;
        }

        return false;
    }

}
