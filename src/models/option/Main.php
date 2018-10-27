<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 31.07.18
 * Time: 21:21
 */

namespace dashboard\models\option;

use dashboard\traits\Model;
use yii\base\BootstrapInterface;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\swiftmailer\Mailer;
use yiidreamteam\widgets\timezone\Validator as TimezoneValidator;

/**
 * Application options.
 *
 * @property string $admin_lang
 * @property string $app_name
 * @property string $app_logo
 * @property string $time_zone
 * @property string $mail_gate_host
 * @property string $mail_gate_login
 * @property string $mail_gate_password
 * @property int $mail_gate_port
 * @property string $mail_gate_encryption
 * @property string $white_ips
 * @property string $black_ips
 * @property bool $maintenance_mode
 * @property bool $site_status
 *
 * @package dashboard\models\option
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Main extends IniConfig implements BootstrapInterface
{
    use Model;

    public const FILE_PATH = '@common/data/.app.ini';
    public const FILE_EXAMPLE_PATH = '@vendor/acid23m/yii2-admin/src/.app.ini.example';

    public const LOGO_WIDTH = 256;
    public const LOGO_HEIGHT = 256;

    public const SMTP_ENCTYPE__SSL = 'ssl';
    public const SMTP_ENCTYPE_TLS = 'tls';

    /**
     * @var string Path alias to view with form
     */
    public $view = '@vendor/acid23m/yii2-admin/src/views/option-main/index';
    /**
     * @var array List of possible languages for administrative panel
     */
    public $admin_langs = [
        'ru' => 'Русский',
        'en' => 'English'
    ];

    protected $mail_gate_encryptions;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $options_file_path = \Yii::getAlias(self::FILE_PATH);
        if (!file_exists($options_file_path)) {
            // create file from example
            $example_options_file_path = \Yii::getAlias(self::FILE_EXAMPLE_PATH);
            \copy($example_options_file_path, $options_file_path);
        }

        $this->path = self::FILE_PATH;
        $this->section = 'options';

        parent::init();

        $this->mail_gate_encryptions = [
            self::SMTP_ENCTYPE__SSL => 'SSL',
            self::SMTP_ENCTYPE_TLS => 'TLS'
        ];
    }

    /**
     * {@inheritdoc}
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
            [['maintenance_mode', 'site_status'], 'boolean'],
            [['mail_gate_encryption'], 'in', 'range' => $mail_gate_encryptions(false)],
            [['white_ips', 'black_ips'], 'string', 'max' => TEXT_LENGTH_NORMAL],
            [['white_ips', 'black_ips'], 'match', 'pattern' => '/^([0-9\.\,* ])+$/'],
            [['mail_gate_login'], 'email']
        ];
    }

    /**
     * Enable or disable maintenance mode.
     * @throws InvalidConfigException
     */
    public function triggerMaintenanceMode(): void
    {
        if ($this->maintenance_mode) {
            \Yii::$app->get('maintenanceMode')->enable();
        } else {
            \Yii::$app->get('maintenanceMode')->disable();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app): void
    {
        if ($app instanceof \yii\web\Application) {
            // access by white and black lists
            $white_ips = self::ipListAsArray($this->get('white_ips', '127.0.0.1'));
            $black_ips = self::ipListAsArray($this->get('black_ips'));
            $ip = \Yii::$app->getRequest()->getUserIP();

            if (\in_array($ip, $black_ips, true)) {
                echo 'Blocked!';
                $app->end();
            } elseif ((bool) $this->get('site_status', 1) === false) {
                if (!\in_array($ip, $white_ips, true)) {
                    echo 'Blocked!';
                    $app->end();
                }
            }
        }

        // application name
        $app->name = $this->get('app_name');

        // application time zone
        $app->setTimeZone($this->get('time_zone', 'UTC'));
        $app->getFormatter()->defaultTimeZone = $app->getTimeZone();

        // backend language
        if ($app->id === 'app-backend') {
            $app->language = $this->get('admin_lang', 'ru');
            $app->getFormatter()->locale = $this->get('admin_lang', 'ru');
        }

        $app->getFormatter()->booleanFormat = [
            \Yii::t('yii', 'No'),
            \Yii::t('yii', 'Yes')
        ];

        // mailer
        /** @var Mailer $mailer */
        $mailer = $app->getMailer();
        if (!empty($this->get('mail_gate_host')) && !empty($this->get('mail_gate_port'))) {
            $mailer->setTransport([
                'class' => 'Swift_SmtpTransport',
                'host' => $this->get('mail_gate_host'),
                'username' => $this->get('mail_gate_login'),
                'password' => $this->get('mail_gate_password'),
                'port' => $this->get('mail_gate_port'),
                'encryption' => $this->get('mail_gate_encryption')
            ]);
        }
    }

    /**
     * @static
     * @param string $list
     * @return array
     */
    protected static function ipListAsArray(?string $list): array
    {
        return ($list !== '' && $list !== null)
            ? array_map('trim', explode(',', $list))
            : [];
    }

}
