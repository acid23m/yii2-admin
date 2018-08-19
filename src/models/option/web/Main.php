<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 31.07.18
 * Time: 23:29
 */

namespace dashboard\models\option\web;

use imagetool\components\Image;
use imagetool\helpers\File;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Main settings.
 *
 * @package dashboard\models\option\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Main extends \dashboard\models\option\Main
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        $rules = [
            [['app_logo'], 'image', 'extensions' => ['jpg', 'jpeg', 'png']]
        ];

        return ArrayHelper::merge(parent::rules(), $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'admin_lang' => \Yii::t('dashboard', 'yazik adminki'),
            'app_name' => \Yii::t('dashboard', 'imya prilozheniya'),
            'app_logo' => \Yii::t('dashboard', 'logo'),
            'time_zone' => \Yii::t('dashboard', 'vremennaya zona'),
            'site_status' => \Yii::t('dashboard', 'dostup k saytu'),
            'white_ips' => \Yii::t('dashboard', 'belie ip'),
            'black_ips' => \Yii::t('dashboard', 'chernie ip'),
            'mail_gate_login' => \Yii::t('dashboard', 'imya polzovatelya pochta'),
            'mail_gate_host' => \Yii::t('dashboard', 'imya servera'),
            'mail_gate_password' => \Yii::t('dashboard', 'parol polzovatelya'),
            'mail_gate_port' => \Yii::t('dashboard', 'port'),
            'mail_gate_encryption' => \Yii::t('dashboard', 'zashita soedineniya'),
            'maintenance_mode' => \Yii::t('dashboard', 'rezhim obsluzhivaniya')
        ];
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws \ImageOptimizer\Exception\Exception
     * @throws \Intervention\Image\Exception\NotWritableException
     * @throws \yii\base\Exception
     */
    public function afterValidate(): void
    {
        $logo = UploadedFile::getInstance($this, 'app_logo');

        if ($logo !== null) {
            $old_logo_filename = \Yii::$app->get('option')->app_logo;
            if ($old_logo_filename !== '') {
                $old_logo_file = File::getPath($old_logo_filename);
                try {
                    unlink($old_logo_file);
                } catch (\Throwable $e) {
                }
            }

            $imagetool = new Image($logo->tempName);
            $imagetool->resizeProportional(self::LOGO_WIDTH, null);
            $logo_filename = $imagetool->save($logo->extension);

            $this->set('app_logo', $logo_filename);
        }

        if ($logo === null && \Yii::$app->get('option')->app_logo === '') {
            $image_manager = new ImageManager(['driver' => 'imagick']);
            $str = mb_strtoupper(mb_substr(\Yii::$app->name, 0, 2));
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
