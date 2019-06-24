<?php

namespace dashboard\controllers\web;

use dashboard\models\user\web\User;
use yii\filters\AccessControl;
use yii\filters\HttpCache;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class BaseController.
 *
 * @package dashoard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class BaseController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public $layout = '@vendor/acid23m/yii2-admin/src/views/layouts/main.php';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'httpCache' => [
                'class' => HttpCache::class,
                'sessionCacheLimiter' => 'nocache',
                'cacheControlHeader' => null
            ],

            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'login',
                            'tfa-code',
                            'request-password-reset',
                            'reset-password'
                        ],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['showData']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['addData']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'matchCallback' => static function ($rule, $action) {
                            return (
                                    \Yii::$app->getUser()->can('addData')
                                    && \Yii::$app->getUser()->can('isOwner',
                                        ['id' => \Yii::$app->getRequest()->get('id')])
                                )
                                || \Yii::$app->getUser()->can(User::ROLE_ADMIN);
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'matchCallback' => static function ($rule, $action) {
                            return (
                                    \Yii::$app->getUser()->can('delData')
                                    && \Yii::$app->getUser()->can('isOwner',
                                        ['id' => \Yii::$app->getRequest()->get('id')])
                                )
                                || \Yii::$app->getUser()->can(User::ROLE_ADMIN);
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?']
                    ]
                ]
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'delete-multiple' => ['post']
                ]
            ]
        ];
    }

}
