<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 24.07.18
 * Time: 1:31
 */

namespace dashboard\widgets;

use yii\base\InvalidArgumentException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * Topbar menu.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class TopMenu extends Widget
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
                $this->items = require_once \Yii::getAlias(\dashboard\Module::getInstance()->top_menu);
            } catch (\Throwable $e) {
                \Yii::error($e->getMessage());
            }
        }
    }

    /**
     * Show menu in top panel.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function run()
    {
        return $this->render('top_menu', ['items' => $this->renderItems()]);
    }

    /**
     * Get menu markup.
     * @return string HTML
     * @throws InvalidArgumentException
     */
    private function renderItems(): string
    {
        $items = '';

        foreach ($this->items as &$item) {
            $icon = isset($item['icon']) ? '<i class="fa fa-' . Html::encode($item['icon']) . '"></i>' : '';
            $label = isset($item['label']) ? Html::encode($item['label']) : '';
            $url = isset($item['url']) ? Url::to($item['url']) : '#';

            $badge_options = ['class' => 'badge'];
            if (isset($item['badgeOptions'])) {
                Html::addCssClass($badge_options, $item['badgeOptions']);
            }
            $badge = isset($item['badge'])
                ? Html::tag('span', Html::encode($item['badge']), $badge_options)
                : '';


            if (isset($item['items'])) { // with subitems
                $items .= '<li class="dropdown">';

                $link_options = [
                    'class' => 'dropdown-toggle',
                    'data-toggle' => 'dropdown',
                    'aria-expanded' => 'false'
                ];
                if (!empty($item['badge'])) {
                    Html::addCssClass($link_options, 'info-number');
                    $link = Html::a($icon . ' ' . $label . ' ' . $badge, $url, $link_options);
                } else {
                    $link = Html::a($icon . ' ' . $label . '<span class=" fa fa-angle-down"></span>', $url,
                        $link_options);
                }

                $items .= $link;

                $items .= '<ul class="dropdown-menu list-unstyled" role="menu">';

                /** @var array $subitems */
                $subitems = $item['items'];
                foreach ($subitems as &$subitem) {
                    $subicon = isset($subitem['icon'])
                        ? '<i class="fa fa-' . Html::encode($subitem['icon']) . ' pull-right"></i>'
                        : '';
                    $sublabel = isset($subitem['label']) ? Html::encode($subitem['label']) : '';
                    $suburl = isset($subitem['url']) ? Url::to($subitem['url']) : '#';

                    $subbadge_options = ['class' => 'badge pull-right'];
                    if (isset($subitem['badgeOptions'])) {
                        Html::addCssClass($subbadge_options, $subitem['badgeOptions']);
                    }
                    $subbadge = isset($subitem['badge'])
                        ? Html::tag('span', Html::encode($subitem['badge']), $subbadge_options)
                        : '';

                    if (!empty($subitem['badge'])) {
                        $sublink = Html::a(
                            $subbadge . ' ' . Html::tag('span', $subicon . ' ' . $sublabel),
                            $suburl
                        );
                    } else {
                        $sublink = Html::a($subicon . ' ' . $sublabel, $suburl);
                    }

                    $items .= '<li>';
                    $items .= $sublink;
                    $items .= '</li>';
                }
                unset($subitem);

                $items .= '</ul>';

            } else { // without subitems
                $items .= '<li>';

                $link_options = [];
                if (!empty($item['badge'])) {
                    Html::addCssClass($link_options, 'info-number');
                    $link = Html::a($icon . ' ' . $label . ' ' . $badge, $url, $link_options);
                } else {
                    $link = Html::a($icon . ' ' . $label, $url, $link_options);
                }

                $items .= $link;
            }

            $items .= '</li>';
        }

        return $items;
    }

}
