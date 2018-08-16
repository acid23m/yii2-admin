<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 05.08.18
 * Time: 16:45
 */

namespace dashboard\controllers\rest;

use dashboard\models\option\rest\Main;
use dashboard\models\user\rest\User;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\filters\auth\HttpBearerAuth;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Class OptionMainController.
 *
 * @package dashboard\controllers\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class OptionMainController extends BaseRestController
{
    /**
     * @inheritdoc
     */
    public function authMethods(): array
    {
        return [
            HttpBearerAuth::class
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs(): array
    {
        return [
            'update' => ['POST']
        ];
    }

    /**
     * @inheritdoc
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []): void
    {
        /** @var Main $model */

        if (!\Yii::$app->getUser()->can(User::ROLE_ADMIN)) {
            throw new ForbiddenHttpException;
        }
    }

    /**
     * Show all options.
     * @return array|Response
     * @throws InvalidConfigException
     */
    public function actionView()
    {
        $model = $this->findModel();

        return $model->getAll();
    }

    /**
     * Update options.
     * @return array|Response
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     */
    public function actionUpdate()
    {
        $model = $this->findModel();

        if (\Yii::$app->get('option')->get('site_status') === null) {
            $model->set('site_status', 1);
        }

        if ($model->load(\Yii::$app->getRequest()->getBodyParams()) && $model->validate() && $model->save()) {
            $model->triggerMaintenanceMode();

            return $model->getAll();
        }

        return $model->getErrors();
    }

    /**
     * Get options model.
     * @return \dashboard\models\option\Main
     * @throws InvalidConfigException
     */
    protected function findModel(): \dashboard\models\option\Main
    {
        /** @var Main $option */
        $model = \Yii::createObject(
            \Yii::$app->components['option']['class']
        );

        if (!($model instanceof \dashboard\models\option\Main)) {
            throw new InvalidConfigException('Option model must be extended from \dashboard\models\option\rest\Main.');
        }

        return $model;
    }

}
