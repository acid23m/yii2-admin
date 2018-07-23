<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 23:16
 */

namespace dashboard\widgets;

use yii\base\InvalidArgumentException;
use yii\base\Widget;
use yii\web\View;

/**
 * Class LeftMenu.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class LeftMenu extends Widget
{
    /**
     * @var array Menu configuration
     */
    public $items = [];

    /**
     * Show menu in left sidebar.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function run()
    {
        return $this->render('left_menu', ['items' => $this->items]);
    }

}
