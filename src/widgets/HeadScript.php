<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.08.18
 * Time: 2:22
 */

namespace dashboard\widgets;

use dashboard\models\option\Script;
use yii\base\Widget;

/**
 * Render scripts in the <head> section.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class HeadScript extends Widget
{
    /**
     * Get content from file.
     * @return string
     */
    public function run(): string
    {
        return (new Script)->head_script;
    }

}
