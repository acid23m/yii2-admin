<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 31.07.18
 * Time: 23:45
 */

namespace dashboard\controllers\web;

use dashboard\models\option\web\Main;
use dashboard\models\user\web\User;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\web\View;

/**
 * Class OptionMainController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class OptionMainController extends BaseController
{
    /**
     * @inheritdoc
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
     * Set main options.
     * @return string|View
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $module = \dashboard\Module::getInstance();

        $class_name = $module !== null && $module->option_model !== null
            ? $module->option_model
            : Main::class;
        $model = \Yii::createObject($class_name);
        if (!($model instanceof \dashboard\models\option\Main)) {
            throw new InvalidConfigException('Option model must be extended from \dashboard\models\option\Main.');
        }

        if (\Yii::$app->get('option')->get('site_status') === null) {
            $model->set('site_status', 1);
        }

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->validate() && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'nastroyki obnovleni'));
        }

        return $this->render($module->option_view, compact('model'));
    }

}
