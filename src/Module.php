<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.07.18
 * Time: 17:35
 */

namespace acid23m\dashboard;

use yii\base\BootstrapInterface;

/**
 * Class Module.
 *
 * @package acid23m\admin
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Module extends \yii\base\Module implements BootstrapInterface
{
    public const MODULE_ID = 'dashboard';

    /**
     * @inheritdoc
     */
    public $id = self::MODULE_ID;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'acid23m\dashboard\controllers';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();


    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app): void
    {
        if ($app->id === 'app-backend') {
            $app->setModule(self::MODULE_ID, ['class' => self::class]);
        }
    }

}
