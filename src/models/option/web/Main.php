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
use yii\web\UploadedFile;
use yiidreamteam\widgets\timezone\Validator as TimezoneValidator;

/**
 * Main settings.
 *
 * @package dashboard\models\option\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Main extends \dashboard\models\option\Main
{
    public const SMTP_ENCTYPE__SSL = 'ssl';
    public const SMTP_ENCTYPE_TLS = 'tls';

    protected $mail_gate_encryptions;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->mail_gate_encryptions = [
            '' => '',
            self::SMTP_ENCTYPE__SSL => 'SSL',
            self::SMTP_ENCTYPE_TLS => 'TLS'
        ];
    }

    /**
     * @inheritdoc
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
            'mail_gate_encryption' => \Yii::t('dashboard', 'zashita soedineniya')
        ];
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function rules(): array
    {
        $admin_lang_list = $this->getList('admin_langs');
        $mail_gate_encryptions = $this->getList('mail_gate_encryptions');

        return [
            [
                [
                    'app_name',
                    'white_ips',
                    'black_ips',
                    'mail_gate_host',
                    'mail_gate_login',
                    'mail_gate_password',
                    'mail_gate_encryption'
                ],
                'trim'
            ],
            [['app_name', 'mail_gate_login'], 'required'],
            [['admin_lang'], 'in', 'range' => $admin_lang_list(false)],
            [
                [
                    'app_name',
                    'mail_gate_host',
                    'mail_gate_login',
                    'mail_gate_password',
                    'mail_gate_encryption'
                ],
                'string',
                'max' => STRING_LENGTH_LONG
            ],
            [['app_logo'], 'image', 'extensions' => ['jpg', 'jpeg', 'png']],
            [['time_zone'], TimezoneValidator::class],
            [['mail_gate_port'], 'integer'],
            [['site_status'], 'boolean'],
            [['mail_gate_encryption'], 'in', 'range' => $mail_gate_encryptions(false)],
            [['white_ips', 'black_ips'], 'string', 'max' => TEXT_LENGTH_NORMAL],
            [['white_ips', 'black_ips'], 'match', 'pattern' => '/^([0-9\.\,* ])+$/'],
            [['mail_gate_login'], 'email']
        ];
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function afterValidate(): void
    {
        $logo = UploadedFile::getInstance($this, 'app_logo');
        $logo_width = 256;
        $logo_height = 256;

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
            $imagetool->resizeProportional($logo_width, null);
            $logo_filename = $imagetool->save('png');

            $this->set('app_logo', $logo_filename);
        }

        if ($logo === null && \Yii::$app->get('option')->app_logo === '') {
            $image_manager = new ImageManager(['driver' => 'imagick']);
            $str = mb_strtoupper(mb_substr(\Yii::$app->name, 0, 2));
            $_logo = $image_manager
                ->canvas($logo_width, $logo_height, '#666666')
                ->text($str, 70, 160, function (AbstractFont $font) {
                    $font->file(\Yii::getAlias(Image::FONT_FILE));
                    $font->size(112);
                    $font->color('#ffffff');
//                        $font->align('center');
//                        $font->valign('middle');
                })
                ->encode(Image::FORMAT_JPG);

            $imagetool = new Image($_logo);
            $imagetool->resizeProportional($logo_width, null);
            $logo_filename = $imagetool->save('jpg');

            $this->set('app_logo', $logo_filename);
        }

        if ($logo === null && \Yii::$app->get('option')->app_logo !== '') {
            $this->set('app_logo', \Yii::$app->get('option')->app_logo);
        }

        parent::afterValidate();
    }

}
