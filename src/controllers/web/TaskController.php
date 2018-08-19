<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 19.08.18
 * Time: 0:39
 */

namespace dashboard\controllers\web;

use dashboard\models\task\web\Task;
use dashboard\models\user\web\User;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\View;

/**
 * Class TaskController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class TaskController extends BaseController
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
                'roles' => [User::ROLE_ADMIN]
            ]
        ];

        return $behaviors;
    }

    /**
     * Lists all Task models.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Task::find()
        ]);

        return $this->render('index', compact('dataProvider'));
    }

    /**
     * Displays a single Task model.
     * @param integer $id
     * @return string|View
     * @throws NotFoundHttpException
     * @throws InvalidArgumentException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|View|Response
     * @throws InvalidArgumentException
     */
    public function actionCreate()
    {
        $model = new Task();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis dobavlena'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', compact('model'));
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|View|Response
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis obnovlena'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', compact('model'));
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();
        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis udalena'));

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Task models.
     * @param string $ids List of ID (array in json format)
     * @return Response
     * @throws InvalidArgumentException
     * @throws StaleObjectException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDeleteMultiple($ids): Response
    {
        $list = Json::decode($ids);
//        Task::deleteAll(['id' => $list]);
        $tasks = Task::find()->where(['id' => $list])->all();
        foreach ($tasks as $task) {
            $task->delete();
        }
        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapisi udaleni'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task The loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Task
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('dashboard', 'zapis ne sushestvuet'));
    }

}
