<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 05.08.18
 * Time: 15:39
 */

namespace dashboard\controllers\rest;

use dashboard\actions\rest\DeleteAction;
use dashboard\models\user\rest\User;
use dashboard\models\user\rest\UserSearch;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;

/**
 * Class UserController.
 *
 * @package dashboard\controllers\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class UserController extends BaseRestActiveController
{
    /**
     * {@inheritdoc}
     */
    public $modelClass = User::class;

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['delete']['class'] = DeleteAction::class;

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    public function authMethods(): array
    {
        return [
            HttpBearerAuth::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var User $model */

        if ($action === 'delete' || $action === 'create') {
            if (!\Yii::$app->getUser()->can(User::ROLE_ADMIN)) {
                throw new ForbiddenHttpException();
            }
        }

        if ($action === 'update' || $action === 'view') {
            if (
                !\Yii::$app->getUser()->can('isOwner', ['id' => $model->id])
                && !\Yii::$app->getUser()->can(User::ROLE_ADMIN)
            ) {
                throw new ForbiddenHttpException();
            }
        }
    }

    /**
     * Program dates data provider.
     * @return ActiveDataProvider
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function prepareDataProvider(): ActiveDataProvider
    {
        $requestParams = \Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = \Yii::$app->getRequest()->getQueryParams();
        }

        $model = new UserSearch;

        return $model->search($requestParams);
    }

}
