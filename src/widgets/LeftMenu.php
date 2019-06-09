<?php

namespace dashboard\widgets;

use yii\base\InvalidArgumentException;
use yii\base\Widget;
use yii\web\View;

/**
 * Sidebar menu.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class LeftMenu extends Widget
{
    /**
     * @var array Menu configuration
     */
    public $items = [];

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        if (empty($this->items)) {
            try {
                $this->items = require \Yii::getAlias(\dashboard\Module::getInstance()->left_menu);
            } catch (\Throwable $e) {
                \Yii::error($e->getMessage());
            }
        }
    }

    /**
     * Shows menu in left sidebar.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function run()
    {
        return $this->render('left_menu', ['items' => $this->items]);
    }

}
