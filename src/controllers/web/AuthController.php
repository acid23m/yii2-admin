<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 29.07.18
 * Time: 20:53
 */

namespace dashboard\controllers\web;

use dashboard\models\user\web\LoginForm;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\View;

/**
 * Class AuthController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class AuthController extends BaseController
{
    /**
     * @inheritdoc
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
     * Render authorization form.
     * @return string|View|Response
     * @throws InvalidArgumentException
     */
    public function actionLogin()
    {
        $this->layout = 'enter';

        if (!\Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $user = $model->getUser();
            if ($user === null) {
                return $this->render('login', compact('model'));
            }

            // use two-factor authentication
//            if ($user->tfa) {
//                return $this->redirect(['tfa-code', 'ci' => $model->send()]);
//            }

            return $model->login()
                ? $this->goBack()
                : $this->render('login', compact('model'));
        }

        return $this->render('login', compact('model'));
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

}
