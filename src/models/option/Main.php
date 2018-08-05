<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 31.07.18
 * Time: 21:21
 */

namespace dashboard\models\option;

use dashboard\traits\Model;
use yii\base\InvalidArgumentException;
use yiidreamteam\widgets\timezone\Validator as TimezoneValidator;

/**
 * Application options.
 *
 * @package dashboard\models\option
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Main extends IniConfig
{
    use Model;

    public const LOGO_WIDTH = 256;
    public const LOGO_HEIGHT = 256;

    public const SMTP_ENCTYPE__SSL = 'ssl';
    public const SMTP_ENCTYPE_TLS = 'tls';

    /**
     * @var array List of possible languages for administrative panel
     */
    public $admin_langs = [
        'ru' => 'Русский',
        'en' => 'English'
    ];

    protected $mail_gate_encryptions;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->path = \dashboard\Module::getInstance()->option_file;
        $this->section = 'options';

        parent::init();

        $this->mail_gate_encryptions = [
            '' => '',
            self::SMTP_ENCTYPE__SSL => 'SSL',
            self::SMTP_ENCTYPE_TLS => 'TLS'
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
            [['time_zone'], TimezoneValidator::class],
            [['mail_gate_port'], 'integer'],
            [['site_status'], 'boolean'],
            [['mail_gate_encryption'], 'in', 'range' => $mail_gate_encryptions(false)],
            [['white_ips', 'black_ips'], 'string', 'max' => TEXT_LENGTH_NORMAL],
            [['white_ips', 'black_ips'], 'match', 'pattern' => '/^([0-9\.\,* ])+$/'],
            [['mail_gate_login'], 'email']
        ];
    }

}
