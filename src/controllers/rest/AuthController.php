<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 08.05.18
 * Time: 23:33
 */

namespace dashboard\controllers\rest;

use dashboard\models\user\UserIdentity;
use yii\filters\auth\HttpBasicAuth;
use yii\web\Response;

/**
 * Class AuthController.
 *
 * @package dashboard\controllers\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class AuthController extends BaseRestController
{
    /**
     * {@inheritdoc}
     */
    public function authMethods(): array
    {
        return [
            'basicAuth' => [
                'class' => HttpBasicAuth::class,
                'auth' => function ($username, $password) {
                    $user = UserIdentity::findByUsername($username);
                    if ($user !== null && $user->validatePassword($password)) {
                        return $user;
                    }

                    return null;
                },
                'only' => ['login']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs(): array
    {
        return [
            'login' => ['POST']
        ];
    }

    /**
     * Get user with auth token.
     * @return null|array|Response
     * @throws \Throwable
     */
    public function actionLogin()
    {
        /** @var null|UserIdentity $user */
        $user = \Yii::$app->getUser()->getIdentity();
        if ($user === null) {
            return null;
        }

//        $user->generateAccessToken(); // this will logout from another web clients
//        $user->save(false);

        \Yii::$app->getResponse()->getHeaders()->set('X-Auth-Token', $user->access_token);

        $user = $user->toArray();
        unset(
            $user['auth_key'],
            $user['password_hash'],
            $user['password_reset_token']
        );

        return $user;
    }

}
