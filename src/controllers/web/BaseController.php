<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:22
 */

namespace dashboard\controllers\web;

use dashboard\assets\AppAsset;
use dashboard\assets\BootboxAsset;
use dashboard\models\user\web\User;
use yii\filters\AccessControl;
use yii\filters\HttpCache;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ErrorAction;

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
    public $layout = '@vendor/acid23m/yii2-admin/src/views/layouts/main';

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'httpCache' => [
                'class' => HttpCache::class,
                'sessionCacheLimiter' => 'private_no_expire'
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
                        'matchCallback' => function ($rule, $action) {
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
                        'matchCallback' => function ($rule, $action) {
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
                    'delete' => ['POST'],
                    'delete-multiple' => ['POST']
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        $actions = [
            'error' => ErrorAction::class
        ];

        return ArrayHelper::merge(parent::actions(), $actions);
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        AppAsset::register($this->getView());
        BootboxAsset::overrideSystemConfirm();
    }

}
