<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 14.08.18
 * Time: 2:34
 */

namespace dashboard\controllers\web;

use dashboard\models\trash\Trash;
use dashboard\models\trash\TrashableInterface;
use dashboard\models\user\web\User;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\IntegrityException;
use yii\helpers\StringHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class TrashController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class TrashController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'] = [
            [
                'allow' => true,
                'roles' => [User::ROLE_MODER]
            ]
        ];

        return $behaviors;
    }

    /**
     * Show items in the recycle bin.
     * @return string
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function actionIndex(): string
    {
        $data_provider = Trash::getItems();

        return $this->render('index', \compact('data_provider'));
    }

    /**
     * Restore item.
     * @param string $class Model classname
     * @param string $id Model id
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionRestore($class, $id): Response
    {
        try {
            $class = StringHelper::base64UrlDecode($class);
            $id = \unserialize(StringHelper::base64UrlDecode($id));
        } catch (\Throwable $e) {
            throw new BadRequestHttpException('Wrong "class" or "id" parameter.');
        }

        /** @var null|ActiveRecord|TrashableInterface $model */
        try {
            $model = $class::findOne($id);
        } catch (\Throwable $e) {
            $model = null;
            \Yii::error($e->getMessage());
        }

        if ($model === null) {
            throw new NotFoundHttpException('Model not found.');
        }

        try {
            $model->restore();
        } catch (\Throwable $e) {
            \Yii::$app->getSession()->setFlash('error', \Yii::t('dashboard', 'ne mogu vosstanovit'));

            return $this->redirect(['index']);
        }

        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis vosstanovlena'));

        if ($model instanceof TrashableInterface) {
            return $this->redirect($model->getViewUrl());
        }

        return $this->redirect(['index']);
    }

    /**
     * Delete item.
     * @param string $class Model classname
     * @param string $id Model id
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($class, $id): Response
    {
        try {
            $class = StringHelper::base64UrlDecode($class);
            $id = \unserialize(StringHelper::base64UrlDecode($id));
        } catch (\Throwable $e) {
            throw new BadRequestHttpException('Wrong "class" or "id" parameter.');
        }

        /** @var null|ActiveRecord|TrashableInterface $model */
        try {
            $model = $class::findOne($id);
        } catch (\Throwable $e) {
            $model = null;
            \Yii::error($e->getMessage());
        }

        if ($model === null) {
            throw new NotFoundHttpException('Model not found.');
        }

        try {
            $model->delete();
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis udalena'));
        } catch (IntegrityException $e) {
            \Yii::$app->getSession()->setFlash('warning', \Yii::t('dashboard', 'ne udaleno iz za svasannih dannih'));
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage());
            \Yii::$app->getSession()->setFlash('error', 'Error.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Delete items.
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionDeleteMultiple(): Response
    {
        $items = Trash::getItems()->allModels;

        foreach ($items as &$item) {
            /** @var ActiveRecord $model */
            $model = $item['model'];

            try {
                $model->delete();
            } catch (IntegrityException $e) {
                \Yii::$app->getSession()->setFlash('warning',
                    \Yii::t('dashboard', 'ne udaleno iz za svasannih dannih'));
            } catch (\Throwable $e) {
                \Yii::error($e->getMessage());
                \Yii::$app->getSession()->setFlash('error', 'Error.');
            }
        }
        unset($items, $item);

        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapisi udaleni'));

        return $this->redirect(['index']);
    }

}
