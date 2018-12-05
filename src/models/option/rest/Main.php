<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 05.08.18
 * Time: 16:48
 */

namespace dashboard\models\option\rest;

use dashboard\models\user\rest\User;
use imagetool\components\Image;
use imagetool\helpers\File;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * Class Main.
 *
 * @package dashboard\models\option\rest
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Main extends \dashboard\models\option\Main
{
    /**
     * @inheritdoc
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = [
            [
                ['app_logo'],
                function ($attribute, $params) {
                    if (
                        $this->$attribute !== ''
                        && $this->$attribute !== null
                        && !StringHelper::startsWith($this->$attribute, 'data:image/')
                    ) {
                        $this->addError($attribute, \Yii::t('dashboard', 'kartinka kak data uri'));
                    }
                }
            ]
        ];

        return ArrayHelper::merge(parent::rules(), $rules);
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $fields = parent::fields();

        if (!\Yii::$app->getUser()->can(User::ROLE_SUPER)) {
            unset(
                $fields['site_status'],
                $fields['white_ips'],
                $fields['black_ips']
            );
        }

        return $fields;
    }

    /**
     * @inheritdoc
     * @throws \ImageOptimizer\Exception\Exception
     * @throws \Intervention\Image\Exception\NotWritableException
     * @throws \yii\base\Exception
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function afterValidate(): void
    {
        $logo = $this->app_logo === '' || $this->app_logo === null ? null : $this->app_logo;

        if ($logo !== null) {
            $old_logo_filename = \Yii::$app->get('option')->app_logo;
            if ($old_logo_filename !== '') {
                $old_logo_file = File::getPath($old_logo_filename);
                try {
                    \unlink($old_logo_file);
                } catch (\Throwable $e) {
                }
            }

            $imagetool = new Image($logo);
            $ext = File::getExtensionOfDataUri($logo);
            $imagetool->resizeProportional(self::LOGO_WIDTH, null);
            $logo_filename = $imagetool->save($ext);

            $this->set('app_logo', $logo_filename);
        }

        if ($logo === null && \Yii::$app->get('option')->app_logo === '') {
            $image_manager = new ImageManager(['driver' => 'imagick']);
            $str = \mb_strtoupper(\mb_substr(\Yii::$app->name, 0, 2));
            $_logo = $image_manager
                ->canvas(self::LOGO_WIDTH, self::LOGO_HEIGHT, '#666666')
                ->text($str, 70, 160, function (AbstractFont $font) {
                    $font->file(\Yii::getAlias(Image::FONT_FILE));
                    $font->size(112);
                    $font->color('#ffffff');
//                    $font->align('center');
//                    $font->valign('middle');
                })
                ->encode(Image::FORMAT_JPG);

            $imagetool = new Image($_logo);
            $imagetool->resizeProportional(self::LOGO_WIDTH, null);
            $logo_filename = $imagetool->save('jpg');

            $this->set('app_logo', $logo_filename);
        }

        if ($logo === null && \Yii::$app->get('option')->app_logo !== '') {
            $this->set('app_logo', \Yii::$app->get('option')->app_logo);
        }

        parent::afterValidate();
    }

}
