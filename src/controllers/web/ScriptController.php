<?php

namespace dashboard\controllers\web;

use dashboard\models\option\web\Script;
use dashboard\models\user\web\User;
use yii\base\InvalidArgumentException;

/**
 * Class ScriptController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class ScriptController extends BaseController
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
     * Updates settings.
     * @return string
     * @throws InvalidArgumentException
     */
    public function actionIndex(): string
    {
        $model = new Script;

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate() && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'nastroyki obnovleni'));
        }

        return $this->render('index', \compact('model'));
    }

}
