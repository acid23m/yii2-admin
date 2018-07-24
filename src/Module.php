<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.07.18
 * Time: 17:35
 */

namespace dashboard;

use dashboard\widgets\LeftMenu;
use dashboard\widgets\TopMenu;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;

/**
 * Class Module.
 *
 * @package dashboard
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @var array Left menu configuration
     */
    public $left_menu = [];
    /**
     * @var array Top menu configuration
     */
    public $top_menu = [];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'dashboard\controllers';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->defaultRoute = 'home/index';

        \Yii::$app->i18n->translations['dashboard'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@vendor/acid23m/yii2-admin/src/messages'
        ];
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app): void
    {
        \Yii::$container->set(LeftMenu::class, [
            'items' => $this->left_menu
        ]);

        \Yii::$container->set(TopMenu::class, [
            'items' => $this->top_menu
        ]);
    }

}
