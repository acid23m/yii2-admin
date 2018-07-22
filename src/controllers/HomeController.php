<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:46
 */

namespace acid23m\dashboard\controllers;

use yii\web\View;

/**
 * Class HomeController.
 *
 * @package acid23m\dashboard\controllers
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
        return $this->render('index');
    }

}
