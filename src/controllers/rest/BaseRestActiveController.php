<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 22.09.17
 * Time: 14:03
 */

namespace dashboard\controllers\rest;

use yii\filters\auth\CompositeAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\Serializer;
use yii\web\ErrorAction;

/**
 * Class BaseRestActiveController.
 *
 * @package dashboard\controllers\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class BaseRestActiveController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $serializer = [
        'class' => Serializer::class,
        'collectionEnvelope' => 'items'
    ];

    /**
     * @inheritdoc
     */
    public function actions(): array
    {
        $actions = parent::actions();

        $actions['errors'] = [
            'class' => ErrorAction::class
        ];

        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = [
            'class' => CompositeAuth::class,
            'authMethods' => $this->authMethods(),
            'only' => $this->authOnly(),
            'optional' => $this->authOptional()
        ];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => $this->cors()
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ArrayHelper::merge(['options'], $this->authExcept());

        return $behaviors;
    }

    /**
     * Configuration for authentication filter.
     * @return array
     * @see \yii\filters\auth\CompositeAuth
     */
    public function authMethods(): array
    {
        return [];
    }

    /**
     * Configuration for authentication filter (except).
     * @return array
     * @see \yii\filters\auth\CompositeAuth
     */
    public function authExcept(): array
    {
        return [];
    }

    /**
     * Configuration for authentication filter (only).
     * @return array
     * @see \yii\filters\auth\CompositeAuth
     */
    public function authOnly(): array
    {
        return [];
    }

    /**
     * Configuration for authentication filter (optional).
     * @return array
     * @see \yii\filters\auth\CompositeAuth
     */
    public function authOptional(): array
    {
        return [];
    }

    /**
     * Configuration for cross-domain requests.
     * @return array
     * @see \yii\filters\Cors
     */
    public function cors(): array
    {
        return [
            'Origin' => '*',
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['Authorization', 'X-Requested-With', 'Content-Type'],
            'Access-Control-Expose-Headers' => [
                'X-Auth-Token',
                'X-Pagination-Current-Page',
                'X-Pagination-Page-Count',
                'X-Pagination-Per-Page',
                'X-Pagination-Total-Count'
            ],
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Max-Age' => 3600
        ];
    }

}
