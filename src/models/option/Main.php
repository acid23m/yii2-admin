<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 31.07.18
 * Time: 21:21
 */

namespace dashboard\models\option;

use dashboard\traits\Model;

/**
 * Application options.
 *
 * @package dashboard\models\option
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Main extends IniConfig
{
    use Model;

    /**
     * @var array List of possible languages for administrative panel
     */
    public $admin_langs = [
        'ru' => 'Русский',
        'en' => 'English'
    ];

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->path = \dashboard\Module::getInstance()->option_file;
        $this->section = 'options';

        parent::init();
    }

}
