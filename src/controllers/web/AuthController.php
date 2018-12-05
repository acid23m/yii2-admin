<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 29.07.18
 * Time: 20:53
 */

namespace dashboard\controllers\web;

use dashboard\models\user\web\LoginForm;
use dashboard\models\user\web\PasswordResetRequestForm;
use dashboard\models\user\web\ResetPasswordForm;
use dashboard\models\user\web\TfaCodeForm;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class AuthController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class AuthController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->layout = 'enter';
    }

    /**
     * Render authorization form.
     * @return string|Response
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function actionLogin()
    {
        if (!\Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }

        $model = new LoginForm;

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate()) {
            $user = $model->getUser();
            if ($user === null) {
                return $this->render('login', \compact('model'));
            }

            // use two-factor authentication
            if ($user->tfa) {
                return $this->redirect(['tfa-code', 'ci' => $model->send()]);
            }

            return $model->login()
                ? $this->goBack()
                : $this->render('login', \compact('model'));
        }

        return $this->render('login', \compact('model'));
    }

    /**
     * Check 2fa code and login user if the code is correct.
     * @param string $ci Cache ID
     * @return string|Response
     * @throws InvalidArgumentException
     */
    public function actionTfaCode($ci)
    {
        if (!\Yii::$app->getUser()->isGuest) {
            return $this->goHome();
        }

        $ci = Html::encode($ci);

        $data = \Yii::$app->getCache()->get($ci);
        if ($data === false) {
            return $this->redirect(['login']);
        }

        $model = new TfaCodeForm(\compact('ci'));

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate()) {
            return $model->login()
                ? $this->goHome()
                : $this->redirect(['login']);
        }

        return $this->render('tfa-code', \compact('model'));
    }

    /**
     * Unauthorize current user.
     * @return Response
     */
    public function actionLogout(): Response
    {
        \Yii::$app->getUser()->logout();

        return $this->goHome();
    }

    /**
     * Render request password reset form.
     * @return string|Response
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm;

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate()) {
            if ($model->send()) {
                \Yii::$app->getSession()->setFlash(
                    'success',
                    \Yii::t('dashboard', 'proverte pochtu i sleduyte instrukcii')
                );

                return $this->redirect(['login']);
            }

            \Yii::$app->session->setFlash(
                'error',
                \Yii::t('dashboard', 'nevozmozhno otpravit soobshenie')
            );
        }

        return $this->render('request-reset', \compact('model'));
    }

    /**
     * Render password reset form.
     * @param string $token Secret token
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate() && $model->resetPassword()) {
            \Yii::$app->session->setFlash('success', \Yii::t('dashboard', 'noviy parol sohranen'));

            return $this->redirect(['login']);
        }

        return $this->render('reset', \compact('model'));
    }

}
