<?php

namespace dashboard\widgets;

use dashboard\models\option\Script;
use yii\base\Widget;

/**
 * Renders scripts before the </body> tag.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class BodyScript extends Widget
{
    /**
     * Gets content from file.
     * @return string
     */
    public function run(): string
    {
        return (new Script)->body_script;
    }

}
