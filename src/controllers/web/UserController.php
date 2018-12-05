<?php

namespace dashboard\controllers\web;

use dashboard\models\user\web\User;
use dashboard\models\user\web\UserSearch;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
final class UserController extends BaseController
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
                'actions' => ['index'],
                'roles' => ['@']
            ],
            [
                'allow' => true,
                'actions' => ['delete', 'create'],
                'roles' => [User::ROLE_ADMIN]
            ],
            [
                'allow' => true,
                'actions' => ['update', 'update-token', 'view'],
                'matchCallback' => function ($rule, $action) {
                    return \Yii::$app->user->can('isOwner', ['id' => \Yii::$app->request->get('id')])
                        || \Yii::$app->user->can(User::ROLE_ADMIN);
                }
            ]
        ];

        return $behaviors;
    }

    /**
     * Lists all User models.
     * @return string
     * @throws InvalidArgumentException
     */
    public function actionIndex(): string
    {
        $searchModel = new UserSearch;
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     * @throws InvalidArgumentException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws InvalidArgumentException
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'polzovatel dobavlen'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->password = null;

        return $this->render('create', \compact('model'));
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws InvalidArgumentException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'polzovatel obnovlen'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->password = null;

        return $this->render('update', \compact('model'));
    }

    /**
     * Updates access token for an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdateToken($id): Response
    {
        $model = $this->findModel($id);
        $model->generateAccessToken();

        if ($model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'polzovatel obnovlen'));
        } else {
            \Yii::$app->getSession()->setFlash('error', \Yii::t('yii', 'Error'));
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing User model.
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
        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'polzovatel udalen'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('dashboard', 'zapis ne sushestvuet'));
    }

}
