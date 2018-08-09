<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 10.08.18
 * Time: 1:15
 */

namespace dashboard\widgets;

use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\StringHelper;

/**
 * Full width place for panels/html-content at the top of the homepage.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class HomePanel extends Widget
{
    /**
     * @var string (wide|left|right)
     */
    public $x_position = 'right';
    /**
     * @var string (top|bottom)
     */
    public $y_position = 'top';

    /**
     * @inheritdoc
     * @return string
     * @throws InvalidConfigException
     * @throws InvalidArgumentException
     */
    public function run(): string
    {
        $content_source = $this->y_position . '_' . $this->x_position . '_panel';
        $content_config = \dashboard\Module::getInstance()->$content_source;

        if (\is_string($content_config)) {
            if (StringHelper::startsWith($content_config, '@')) { // path alias to content
                $content_path = \Yii::getAlias($content_config);
                if (!file_exists($content_path)) {
                    throw new InvalidConfigException("File '$content_path' not found.");
                }
                $content = file_get_contents($content_path);
            } else { // string with content
                $content = $content_config;
            }
        } elseif (\is_array($content_config)) { // widget with content
            $widget_class = $content_config['class'] ?? null;
            if ($widget_class === null) {
                throw new InvalidConfigException('Widget not defined: class property must be set.');
            }

            $content = \call_user_func($widget_class . '::widget', $content_config);
        } else {
            throw new InvalidConfigException("'$content_source' property must be type of string or array: " . \gettype($content_config) . ' given.');
        }

        return $content;
    }

}
