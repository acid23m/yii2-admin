<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/** @var yii\web\View $this */
/** @var yii\gii\generators\crud\Generator $generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use <?= ltrim($generator->modelClass, '\\') ?>;
use dashboard\models\user\web\User;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\base\InvalidArgumentException;
use yii\db\IntegrityException;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\View;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 *
 * @package <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>
 */
final class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
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
                'actions' => ['index', 'view'],
                'roles' => [User::ROLE_DEMO]
            ],
            [
                'allow' => true,
                'actions' => ['create', 'update', 'delete', 'delete-multiple', 'restore', 'save-order'],
                'roles' => [User::ROLE_MODER]
            ]
        ];

        return $behaviors;
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function actionIndex()
    {
<?php if (!empty($generator->searchModelClass)): ?>
        $searchModel = new <?= $searchModelAlias ?? $searchModelClass ?>();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        // sort items
//        $data = <?= $modelClass ?>::find()->published()->actual()->ordered()->all();
//        $sortItems = [];
//        foreach ($data as &$item) {
//            $sortItems[$item->position] = $item;
//        }

        return $this->render('index', compact('searchModel', 'dataProvider', 'sortItems'));
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find()
        ]);

        // sort items
//        $data = <?= $modelClass ?>::find()->published()->actual()->ordered()->all();
//        $sortItems = [];
//        foreach ($data as &$item) {
//            $sortItems[$item->position] = $item;
//        }

        return $this->render('index', compact('dataProvider', 'sortItems'));
<?php endif ?>
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return string|View
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(<?= $actionParams ?>)
    {
        return $this->render('view', [
            'model' => $this->findModel(<?= $actionParams ?>)
        ]);
    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|View|Response
     * @throws InvalidArgumentException
     */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis dobavlena'));
            return $this->redirect(['view', <?= $urlParams ?>]);
        }

        return $this->render('create', compact('model'));
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return string|View|Response
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $model = $this->findModel(<?= $actionParams ?>);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis obnovlena'));
            return $this->redirect(['view', <?= $urlParams ?>]);
        }

        return $this->render('update', compact('model'));
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(<?= $actionParams ?>): Response
    {
        $model = $this->findModel(<?= $actionParams ?>);

        try {
            $model->delete();
            // $model->softDelete();
        } catch (IntegrityException $e) {
            \Yii::$app->getSession()->setFlash('warning', \Yii::t('dashboard', 'ne udaleno iz za svasannih dannih'));

            return $this->redirect(['view', <?= $urlParams ?>]);
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage());
            \Yii::$app->getSession()->setFlash('error', 'Error.');

            return $this->redirect(['view', <?= $urlParams ?>]);
        }

        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis udalena'));
        // \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis v korzine'));

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing <?= $modelClass ?> models.
     * @param string $ids List of ID (array in json format)
     * @return Response
     * @throws InvalidArgumentException
     */
    public function actionDeleteMultiple($ids): Response
    {
        $list = Json::decode($ids);

        try {
            <?= $modelClass ?>::deleteAll(['id' => $list]);
            /*$models = <?= $modelClass ?>::find()->where(['id' => $list])->all(); // use beforeDelete and afterDelete
            foreach ($models as $model) {
                $model->delete();
                // $model->softDelete();
            }*/
        } catch (IntegrityException $e) {
            \Yii::$app->getSession()->setFlash('warning', \Yii::t('dashboard', 'ne udaleno iz za svasannih dannih'));

            return $this->redirect(['index']);
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage());
            \Yii::$app->getSession()->setFlash('error', 'Error.');

            return $this->redirect(['index']);
        }

        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapisi udaleni'));

        return $this->redirect(['index']);
    }

    /**
     * Restore <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return Response
     * @throws NotFoundHttpException
     */
//    public function actionRestore(<?= $actionParams ?>): Response
//    {
//        $model = $this->findModel(<?= $actionParams ?>);
//        $model->restore();
//
//        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis vosstanovlena'));
//
//        return $this->redirect(['view', <?= $urlParams ?>]);
//    }

    /**
     * Change positions of an existing <?= $modelClass ?> models.
     * @param string $ids List of ID (array in json format)
     * @return Response
     * @throws NotFoundHttpException
     * @throws InvalidArgumentException
     */
//    public function actionSaveOrder($ids): Response
//    {
//        /** @var array $list */
//        $list = Json::decode($ids);
//        foreach ($list as $index => $id) {
//            $model = $this->findModel($id);
//            $model->moveToPosition($index + 1); // index is zero based, but position is not
//        }
//
//        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapis obnovlena'));
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?= $modelClass ?> The loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>): <?= $modelClass ?>
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(\Yii::t('dashboard', 'zapis ne sushestvuet'));
    }

}
