<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.07.18
 * Time: 17:35
 */

namespace dashboard;

use dashboard\models\user\UserIdentity;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\i18n\PhpMessageSource;
use yii\rbac\DbManager;
use yii\rest\UrlRule;
use yii\web\User;

/**
 * Class Module.
 *
 * @package dashboard
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Module extends \yii\base\Module implements BootstrapInterface
{
    public const DB_USER = 'dbAdminUser';
    public const DB_TASK = 'dbAdminTask';

    /**
     * @var array Left menu configuration
     */
    public $left_menu = [];
    /**
     * @var array Top menu configuration
     */
    public $top_menu = [];
    /**
     * @var array User roles
     */
    public $user_roles = [];
    /**
     * @var null|callable User rules (RBAC)
     */
    public $user_rules;
    /**
     * @var string|array Html content (not encoded) for homepage.
     * It can be string with content, string with path alias to file with content or
     * array with widget configuration.
     */
    public $top_wide_panel = '';
    /**
     * @var string|array Html content (not encoded) for homepage.
     * It can be string with content, string with path alias to file with content or
     * array with widget configuration.
     */
    public $bottom_wide_panel = '';
    /**
     * @var string|array Html content (not encoded) for homepage.
     * It can be string with content, string with path alias to file with content or
     * array with widget configuration.
     */
    public $top_left_panel = '';
    /**
     * @var string|array Html content (not encoded) for homepage.
     * It can be string with content, string with path alias to file with content or
     * array with widget configuration.
     */
    public $top_right_panel = '';
    /**
     * @var string|array Html content (not encoded) for homepage.
     * It can be string with content, string with path alias to file with content or
     * array with widget configuration.
     */
    public $bottom_left_panel = '';
    /**
     * @var string|array Html content (not encoded) for homepage.
     * It can be string with content, string with path alias to file with content or
     * array with widget configuration.
     */
    public $bottom_right_panel = '';
    /**
     * @var array List of configuration of soft-deletable items
     */
    public $trash_items = [];
    /**
     * @var array Sitemap configuration
     */
    public $sitemap_items = [];
    /**
     * @var array Search configuration
     */
    public $search_items = [];

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->defaultRoute = 'home/index';

        // common configuration
        \Yii::configure(\Yii::$app, [
            'components' => [
                self::DB_USER => [
                    'class' => Connection::class,
                    'dsn' => 'sqlite:@common/data/userdata.db',
                    'schemaCacheDuration' => 3600,
                    'on afterOpen' => function (Event $event) {
                        /** @var Connection $connection */
                        $connection = $event->sender;
                        $connection->createCommand('PRAGMA foreign_keys = ON;')->execute();
                        $connection->createCommand('PRAGMA case_sensitive_like = false;')->execute();
                        $connection->createCommand('PRAGMA count_changes = false;')->execute();
//                        $connection->createCommand('PRAGMA journal_mode = OFF;')->execute();
                        $connection->createCommand('PRAGMA synchronous = NORMAL;')->execute();
                    }
                ],
                self::DB_TASK => [
                    'class' => Connection::class,
                    'dsn' => 'sqlite:@common/data/taskdata.db',
                    'schemaCacheDuration' => 3600,
                    'on afterOpen' => function (Event $event) {
                        /** @var Connection $connection */
                        $connection = $event->sender;
                        $connection->createCommand('PRAGMA foreign_keys = ON;')->execute();
                        $connection->createCommand('PRAGMA case_sensitive_like = false;')->execute();
                        $connection->createCommand('PRAGMA count_changes = false;')->execute();
//                        $connection->createCommand('PRAGMA journal_mode = OFF;')->execute();
                        $connection->createCommand('PRAGMA synchronous = NORMAL;')->execute();
                    }
                ]
            ]
        ]);

        $this->modules = [
            'multipage' => [
                'class' => \multipage\Module::class,
                'layout' => '@vendor/acid23m/yii2-admin/src/views/layouts/main.php'
            ]
        ];

        // web configuration
        $this->initWeb();
        // backend configuration
        $this->initBackFront();
        // rest configuration
        $this->initRest();
        // console configuration
        $this->initConsole();

        // parameters
        $this->params['user.passwordResetTokenExpire'] = 3600; // 1 hour
        $this->params['user.accessTokenExpire'] = 86400; // 1 day

        $this->params['author.name'] = 'Cipa';
        $this->params['author.url'] = 'http://cipastudiya.ru';
    }

    /**
     * {@inheritdoc}
     * @param \yii\web\Application|\yii\console\Application $app
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function bootstrap($app): void
    {
        // define constants
        \defined('STRING_LENGTH_SHORT') or \define('STRING_LENGTH_SHORT', 50);
        \defined('STRING_LENGTH_NORMAL') or \define('STRING_LENGTH_NORMAL', 150);
        \defined('STRING_LENGTH_LONG') or \define('STRING_LENGTH_LONG', 255);

        \defined('TEXT_LENGTH_NORMAL') or \define('TEXT_LENGTH_NORMAL', 65535);
        \defined('TEXT_LENGTH_MEDIUM') or \define('TEXT_LENGTH_MEDIUM', 16777215);
        \defined('TEXT_LENGTH_LONG') or \define('TEXT_LENGTH_LONG', 4294967296);

        \defined('STANDARD_DATE_FORMAT') or \define('STANDARD_DATE_FORMAT', 'Y-m-d');
        \defined('STANDARD_DATETIME_FORMAT') or \define('STANDARD_DATETIME_FORMAT', 'Y-m-d H:i:s');

        \defined('PERM_DIR') or \define('PERM_DIR', 0775);
        \defined('PERM_FILE') or \define('PERM_FILE', 0664);


        // env settings
        $dotenv = new \Dotenv\Dotenv(\Yii::getAlias('@root'));
        $dotenv->load();

        if ($app instanceof \yii\web\Application) {
            // rest api
            if ($app->id === 'app-remote') {
                \Yii::$app->getUrlManager()->addRules([
                    "GET,POST,HEAD /{$this->id}/auth/<_a:[\w\d\-_]+>" => "/{$this->id}/auth/<_a>",
                    "OPTIONS /{$this->id}/auth/<_a:[\w\d\-_]+>" => "/{$this->id}/auth/options",
                    [
                        'class' => UrlRule::class,
                        'controller' => [
                            "{$this->id}/user"
                        ],
                        'pluralize' => false
                    ],
                    "GET,HEAD /{$this->id}/option-main" => "/{$this->id}/option-main/view",
                    "POST /{$this->id}/option-main" => "/{$this->id}/option-main/update"
                ], false);
            }
        } elseif ($app instanceof \yii\console\Application) {
            // default host info for console commands
            $domain = getenv('SITE_DOMAIN');
            \Yii::$app->get('urlManager')->setHostInfo('http://' . $domain);
            \Yii::$app->get('urlManagerFrontend')->setHostInfo('http://' . $domain);
            \Yii::$app->get('urlManagerBackend')->setHostInfo('http://' . $domain);
            \Yii::$app->get('urlManagerRemote')->setHostInfo('http://' . $domain);
        }

        $app->getI18n()->translations['dashboard'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@vendor/acid23m/yii2-admin/src/messages'
        ];


        // check access user data
        $user_db_file = \Yii::getAlias('@common/data/userdata.db');

        if (!file_exists($user_db_file)) {
            FileHelper::createDirectory(\Yii::getAlias('@common/data'));
            $user_db = new \SQLite3($user_db_file);

            $user_db->exec(<<<'SQL'
CREATE TABLE "user" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "username" VARCHAR(50) NOT NULL,
    "email" VARCHAR(50) NOT NULL,
    "password_hash" VARCHAR(128) NOT NULL DEFAULT (NULL),
    "access_token" VARCHAR,
    "auth_key" VARCHAR,
    "password_reset_token" VARCHAR,
    "role" VARCHAR NOT NULL,
    "ip" VARCHAR(255),
    "note" TEXT,
    "avatar" BLOB,
    "tfa" INTEGER NOT NULL DEFAULT (0),
    "status" INTEGER NOT NULL DEFAULT (1),
    "last_access" DATETIME,
    "created_at" DATETIME,
    "updated_at" DATETIME
);
SQL
            );
            $user_db->exec(<<<'SQL'
CREATE TABLE "auth_rule" (
    "name" VARCHAR(64) NOT NULL,
    "data" TEXT,
    "created_at" INTEGER,
    "updated_at" INTEGER,
    PRIMARY KEY ("name")
);
SQL
            );
            $user_db->exec(<<<'SQL'
CREATE TABLE "auth_item" (
   "name" VARCHAR(64) NOT NULL,
   "type" INTEGER NOT NULL,
   "description" TEXT,
   "rule_name" VARCHAR(64),
   "data" TEXT,
   "created_at" INTEGER,
   "updated_at" INTEGER,
   PRIMARY KEY ("name"),
   FOREIGN KEY ("rule_name") REFERENCES "auth_rule" ("name") ON DELETE SET NULL ON UPDATE CASCADE
);
SQL
            );
            $user_db->exec('CREATE INDEX "auth_item_type_idx" ON "auth_item" ("type");');
            $user_db->exec(<<<'SQL'
CREATE TABLE "auth_item_child" (
   "parent" VARCHAR(64) NOT NULL,
   "child" VARCHAR(64) NOT NULL,
   PRIMARY KEY ("parent","child"),
   FOREIGN KEY ("parent") REFERENCES "auth_item" ("name") ON DELETE CASCADE ON UPDATE CASCADE,
   FOREIGN KEY ("child") REFERENCES "auth_item" ("name") ON DELETE CASCADE ON UPDATE CASCADE
);
SQL
            );
            $user_db->exec(<<<'SQL'
CREATE TABLE "auth_assignment" (
   "item_name" VARCHAR(64) NOT NULL,
   "user_id" VARCHAR(64) NOT NULL,
   "created_at" INTEGER,
   PRIMARY KEY ("item_name","user_id"),
   FOREIGN KEY ("item_name") REFERENCES "auth_item" ("name") ON DELETE CASCADE ON UPDATE CASCADE
);
SQL
            );
            $user_db->exec('CREATE INDEX "auth_assignment_user_id_idx" ON "auth_assignment" ("user_id");');

            chmod($user_db_file, PERM_FILE);
        }

        // check tasks data
        $task_db_file = \Yii::getAlias('@common/data/taskdata.db');

        if (!file_exists($task_db_file)) {
            $task_db = new \SQLite3($task_db_file);

            $task_db->exec(<<<'SQL'
CREATE TABLE "task" (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "name" VARCHAR NOT NULL,
    "min" VARCHAR NOT NULL DEFAULT '*',
    "hour" VARCHAR NOT NULL DEFAULT '*',
    "day" VARCHAR NOT NULL DEFAULT '*',
    "month" VARCHAR NOT NULL DEFAULT '*',
    "weekDay" VARCHAR NOT NULL DEFAULT '*',
    "command" VARCHAR NOT NULL,
    "status" INTEGER NOT NULL DEFAULT 0
);
SQL
            );

            chmod($task_db_file, PERM_FILE);
        }

        // check adminer
        $adminer_dir = \Yii::getAlias('@backend/web/adminer');
        if (!file_exists($adminer_dir)) {
            FileHelper::createDirectory($adminer_dir);
        }

        $adminer_index = \Yii::getAlias('@vendor/dg/adminer-custom/index.php');
        $adminer_index_sl = $adminer_dir . '/index.php';
        try {
            \linkinfo($adminer_index_sl);
        } catch (\Throwable $e) {
            \symlink($adminer_index, $adminer_index_sl);
        }

        $adminer_css = \Yii::getAlias('@vendor/dg/adminer-custom/adminer.css');
        $adminer_css_sl = $adminer_dir . '/adminer.css';
        try {
            \linkinfo($adminer_css_sl);
        } catch (\Throwable $e) {
            \symlink($adminer_css, $adminer_css_sl);
        }
    }


    /**
     * Function for web.
     */
    private function initWeb(): void
    {
        if (\Yii::$app instanceof \yii\web\Application) {
            \Yii::configure(\Yii::$app, [
                'components' => [
                    'authManager' => [
                        'class' => DbManager::class,
                        'db' => self::DB_USER
                    ]
                ]
            ]);

            \Yii::$app->modules = ArrayHelper::merge(\Yii::$app->modules, [
                \imagetool\Module::DEFAULT_ID => [
                    'class' => \imagetool\Module::class,
                    'controllerNamespace' => 'imagetool\controllers\web'
                ]
            ]);
        }

    }

    /**
     * Function for backend/frontend.
     */
    private function initBackFront(): void
    {
        if (\Yii::$app instanceof \yii\web\Application && $this->controllerNamespace === 'dashboard\controllers\web') {
            \Yii::configure(\Yii::$app, [
                'components' => [
                    'user' => [
                        'class' => User::class,
                        'identityClass' => UserIdentity::class,
                        'enableAutoLogin' => true,
                        'identityCookie' => [
                            'name' => '_backendUser',
                            'path' => '/admin',
                            'httpOnly' => true
                        ],
                        'loginUrl' => ["/{$this->id}/auth/login"]
                    ]
                ]
            ]);
        }
    }

    /**
     * Function for rest api.
     */
    private function initRest(): void
    {
        if (\Yii::$app instanceof \yii\web\Application && $this->controllerNamespace === 'dashboard\controllers\rest') {
            \Yii::configure(\Yii::$app, [
                'components' => [
                    'user' => [
                        'class' => User::class,
                        'identityClass' => UserIdentity::class,
                        'enableAutoLogin' => false,
                        'enableSession' => false,
                        'identityCookie' => [
                            'name' => '_apiUser',
                            'path' => '/api',
                            'httpOnly' => true
                        ],
                        'loginUrl' => null
                    ]
                ]
            ]);
        }
    }

    /**
     * Function for console.
     */
    private function initConsole(): void
    {
        if (\Yii::$app instanceof \yii\console\Application) {
            \Yii::configure(\Yii::$app, [
                'components' => [
                    'authManagerBackend' => [
                        'class' => DbManager::class,
                        'db' => self::DB_USER
                    ]
                ]
            ]);
        }
    }

}
