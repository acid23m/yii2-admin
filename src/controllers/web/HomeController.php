<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:46
 */

namespace dashboard\controllers\web;

use yii\web\View;
use dashboard\models\log\LogSearch;

/**
 * Class HomeController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class HomeController extends BaseController
{
    /**
     * Show home page.
     * @return string|View
     * @throws \yii\base\InvalidArgumentException
     */
    public function actionIndex()
    {
        // Log
        $logSearchModel = new LogSearch;
        $logDataProvider = $logSearchModel->search(
            \Yii::$app->getRequest()->getQueryParams()
        );

        return $this->render('index', compact('logSearchModel', 'logDataProvider'));
    }

}
