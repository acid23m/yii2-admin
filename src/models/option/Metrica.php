<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.08.18
 * Time: 0:52
 */

namespace dashboard\models\option;

/**
 * Class Metrika.
 *
 * @property string $google_analytics
 * @property string $yandex_metrika
 *
 * @package dashboard\models\option
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Metrica extends IniConfig
{
    public const INI_FILE_PATH = '@common/data/.script.ini';
    public const INI_FILE_EXAMPLE_PATH = '@vendor/acid23m/yii2-admin/src/.script.ini.example';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $options_file_path = \Yii::getAlias(self::INI_FILE_PATH);
        if (!\file_exists($options_file_path)) {
            // create file from example
            $example_options_file_path = \Yii::getAlias(self::INI_FILE_EXAMPLE_PATH);
            \copy($example_options_file_path, $options_file_path);
        }

        $this->path = self::INI_FILE_PATH;
        $this->section = 'options';

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['google_analytics', 'yandex_metrika'], 'trim'],
            [['google_analytics', 'yandex_metrika'], 'string', 'max' => STRING_LENGTH_SHORT]
        ];
    }

}
