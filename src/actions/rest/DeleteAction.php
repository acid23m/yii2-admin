<?php

namespace dashboard\actions\rest;

use yii\base\InvalidArgumentException;
use yii\db\IntegrityException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class DeleteAction.
 *
 * @package dashboard\actions\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class DeleteAction extends \yii\rest\DeleteAction
{
    /**
     * {@inheritdoc}
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     */
    public function run($id): void
    {
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            \call_user_func($this->checkAccess, $this->id, $model);
        }

        try {
            $result = $model->delete();
        } catch (IntegrityException $e) {
            throw new BadRequestHttpException('Not deleted due to related data.');
        } catch (\Throwable $e) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        if ($result === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        \Yii::$app->getResponse()->setStatusCode(204);
    }

}
