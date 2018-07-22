<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:22
 */

namespace acid23m\dashboard\controllers;

use yii\filters\HttpCache;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Class BaseController.
 *
 * @package acid23m\dashoard
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class BaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public $layout = '@vendor/acid23m/yii2-admin/src/views/layouts/main';

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'httpCache' => [
                'class' => HttpCache::class,
                'sessionCacheLimiter' => 'private_no_expire'
            ],

            /*'access' => [
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
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?']
                    ]
                ]
            ],*/

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'delete-multiple' => ['post']
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions(): array
    {
        $actions = [
            'error' => ErrorAction::class
        ];

        return ArrayHelper::merge(parent::actions(), $actions);
    }

}
