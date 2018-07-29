<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 20.07.18
 * Time: 17:35
 */

namespace dashboard;

use dashboard\models\user\UserIdentity;
use dashboard\widgets\LeftMenu;
use dashboard\widgets\TopMenu;
use yii\base\BootstrapInterface;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\db\Connection;
use yii\helpers\FileHelper;
use yii\i18n\I18N;
use yii\i18n\PhpMessageSource;
use yii\rbac\DbManager;
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
     * @inheritdoc
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
                    'schemaCacheDuration' => 3600
                ],
                'i18n' => [
                    'class' => I18N::class,
                    'translations' => [
                        'dashboard' => [
                            'class' => PhpMessageSource::class,
                            'basePath' => '@vendor/acid23m/yii2-admin/src/messages'
                        ]
                    ]
                ]
            ]
        ]);

        if (\Yii::$app instanceof \yii\web\Application) {
            // web configuration
            \Yii::configure(\Yii::$app, [
                'components' => [
                    'authManager' => [
                        'class' => DbManager::class,
                        'db' => self::DB_USER
                    ]
                ]
            ]);
            // configure user in backend
            if ($this->controllerNamespace === 'dashboard\controllers\web') {
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
            // configure user in rest
            if ($this->controllerNamespace === 'dashboard\controllers\rest') {
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
        } else {
            // console configuration
            \Yii::configure(\Yii::$app, [
                'components' => [
                    'authManagerBackend' => [
                        'class' => DbManager::class,
                        'db' => self::DB_USER
                    ]
                ]
            ]);
        }

        $this->params['user.passwordResetTokenExpire'] = 3600; // 1 hour
        $this->params['user.accessTokenExpire'] = 86400; // 1 day

        $this->params['author.name'] = 'Cipa';
        $this->params['author.url'] = 'http://cipastudiya.ru';
    }

    /**
     * @inheritdoc
     * @param $app
     * @throws Exception
     * @throws InvalidArgumentException
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


        if ($app instanceof \yii\web\Application) {
            // configure left menu
            \Yii::$container->set(LeftMenu::class, [
                'items' => $this->left_menu
            ]);
            // configure top menu
            \Yii::$container->set(TopMenu::class, [
                'items' => $this->top_menu
            ]);
        }


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
    }

}
