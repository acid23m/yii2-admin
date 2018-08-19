Admin
=====
Admin module for my dockerized Yii2 application.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist acid23m/yii2-admin "dev-master"
```

or add

```
"acid23m/yii2-admin": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, do next:

- Add module in `common/config/main.php`.

```php
'components' => [
    'maintenanceMode' => [
        'class' => \brussens\maintenance\MaintenanceMode::class,
        'urls' => [
            'debug/default/toolbar',
            'debug/default/view'
        ]
    ]
],
```

- Add module in `backend/config/main.php`.

```php
'bootstrap' => [
    'log',
    'option',
    'dashboard'
],

'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'controllerNamespace' => 'dashboard\controllers\web',
        'left_menu' => [ // move it to separate file and include here
            'section' => [ // section header
                [
                    'label' => 'Menu Item', // menu item label
                    'url' => '#', // url compatible with Url::to()
                    'icon' => 'th', // fontawesome icon id
                    'badge' => '123', // badge text
                    'badgeOptions' => ['class' => 'label-success'] // badge config
                ]
            ]
        ],
        'top_menu' => [ // move it to separate file and include here
            [
                'label' => 'Menu Item',
                'url' => '#',
                'icon' => 'th',
                'badge' => '123',
                'badgeOptions' => ['class' => 'bg-green']
            ],
            [
                'icon' => 'circle-o',
                'items' => [
                    [
                        'label' => 'Subitem',
                        'badge' => 'str',
                        'badgeOptions' => 'bg-red'
                    ],
                    [
                        'label' => 'Site',
                        'url' => '/'
                    ]
                ]
            ]
        ],
        'user_roles' => [
            // additional user roles here
            // default roles are demonstration, author, moderator, administrator, root
            'agent' => 'Agent'
        ],
        // additional content for dashboard homepage
        'top_wide_panel' => '<p class="bg-red" style="padding:10px">Top Wide HTML Content</p>',
        'top_right_panel' => [
            'class' => '\backend\widgets\TopRightPanel'
        ],
        'bottom_left_panel' => '@backend/views/bottom_left_panel.php',
        'bottom_wide_panel' => file_get_contents(__DIR__ . '/../views/panels/bottom_wide_panel.php'),
        // items that can be soft deleted (removed to the recycle bin) and can be shown in the recycle bin
        'trash_items' => [
            \backend\modules\post\models\PostTrash::class // must implement \dashboard\models\trash\TrashableInterface
        ],
        // sitemap config
        'sitemap_items' => [
            'class' => \common\models\sitemap\Sitemap::class // must implement \dashboard\models\sitemap\SitemapConfigInterface
        ]
    ]
],

'components' => [
    'option' => [
        'class' => \dashboard\models\option\web\Main::class, // original class
        //'class' => \backend\models\option\Option::class, // own class must be extended from \dashboard\models\option\web\Main
        //'view' => '@backend/views/option/index', // example in vendor/acid23m/yii2-admin/src/views/option-main/index.php
    ]
],
```

- Add module in `console/config/main.php`.

```php
'bootstrap' => [
    'log',
    'dashboard'
],

'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'controllerNamespace' => 'dashboard\commands',
        'user_roles' => [
            // additional user roles here
            // default roles are demonstration, author, moderator, administrator, root
            'agent' => 'Agent'
        ],
        'user_rules' => function (\yii\rbac\ManagerInterface &$auth, array $default_permissions, array $default_roles) {
            // additional user rules
            // default permissions are showData, addData, updateData, delData, isOwner (rule)
            $receivePayment = $auth->createPermission('receivePayment');
            $receivePayment->description = 'Receive payment';
            $auth->add($receivePayment);

            $agent = $auth->createRole('agent');
            $agent->description = 'Agent';
            $auth->add($agent);
            $auth->addChild($agent, $default_roles[\dashboard\models\user\web\User::ROLE_MODER]);
            $auth->addChild($agent, $default_permissions['isOwner']);
            $auth->addChild($agent, $receivePayment);
        },
        // sitemap config
        'sitemap_items' => [
            'class' => \common\models\sitemap\Sitemap::class // must implement \dashboard\models\sitemap\SitemapConfigInterface
        ]
    ]
],
```

- Add module in `remote/config/main.php`.

```php
'bootstrap' => [
    'log',
    'dashboard'
],

'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'controllerNamespace' => 'dashboard\controllers\rest'
    ]
],

'components' => [
    'option' => [
        'class' => \dashboard\models\option\rest\Main::class, // original class
        //'class' => \backend\models\option\Option::class, // own class must be extended from \dashboard\models\option\rest\Main
    ]
],
```

- Add components in `frontend/config/main`.

```php
'bootstrap' => [
    'log',
    'option',
    'maintenanceMode'
],

'modules' => [
    'imagetool' => [
        'class' => \imagetool\Module::class,
        'controllerNamespace' => 'imagetool\controllers\web'
    ]
],

'components' => [
    'option' => [
        'class' => \dashboard\models\option\web\Main::class // original class
        //'class' => \backend\models\option\Option::class // must be extended from \dashboard\models\option\web\Main
    ]
],
```

- Every controller in backend must be extended from `\dashboard\controllers\web\BaseController`.

- Add rule to UrlManager config.

```php
'rules' => [
    '/' => '/dashboard/home/index'
]
```

Rest Api examples in *html* directory.


App options
-----------

Application options locate in `common/data/.app.ini` file.

If you want to add additional options, you must create class and extend it from

- `\dashboard\models\option\web\Main` for backend and frontend

```php
namespace backend\models\option;

use yii\helpers\ArrayHelper;

/**
 * Extended application options.
 *
 * @property string $app_lang
 * @property string $service_api_key
 *
 * @package backend\models\option
 */
final class Option extends \dashboard\models\option\web\Main
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = [
            [['app_lang'], 'string', 'max' => 7],
            [['service_api_key'], 'string', 'max' => STRING_LENGTH_LONG]
        ];

        return ArrayHelper::merge(parent::rules(), $rules);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        $labels = [
            'app_lang' => \Yii::t('app', 'yazyk prilozheniya'),
            'service_api_key' => \Yii::t('app', 'api kluch dlya servisa')
        ];

        return ArrayHelper::merge(parent::attributeLabels(), $labels);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        parent::bootstrap();

        // frontend language
        if ($app->id === 'app-frontend') {
            $app->language = $this->get('app_lang', 'ru');
            $app->getFormatter()->locale = $this->get('app_lang', 'ru');
        }
    }

}
```

- `\dashboard\models\option\rest\Main` for rest

```php
namespace remote\models\option;

use yii\helpers\ArrayHelper;

/**
 * Extended application options.
 *
 * @property string $app_lang
 * @property string $service_api_key
 *
 * @package remote\models\option
 */
final class Option extends \dashboard\models\option\rest\Main
{
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = [
            [['app_lang'], 'string', 'max' => 7],
            [['service_api_key'], 'string', 'max' => STRING_LENGTH_LONG]
        ];

        return ArrayHelper::merge(parent::rules(), $rules);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app): void
    {
        parent::bootstrap();

        // frontend language
        if ($app->id === 'app-frontend') {
            $app->language = $this->get('app_lang', 'ru');
            $app->getFormatter()->locale = $this->get('app_lang', 'ru');
        }
    }

}
```

Next create view file for example at `backend/views/option/index.php`.
As template use file `vendor/acid23m/yii2-admin/views/option-main/index.php`.
Copy content. Change phpdoc for *$model* variable from

```php
/** @var \dashboard\models\option\web\Main $model */
```

to

```php
/** @var \backend\models\option\Option $model */
```

And put form input somewhere.

```php
<?= $form->field($model, 'service_api_key')->textInput(['maxlength' => true]) ?>
```

Finally update config settings

- in `backend/config/main.php`

```php
'bootstrap' => [
    'log',
    'option',
    'dashboard'
],

'components' => [
    'option' => [
        'class' => \backend\models\option\Option::class,
        'view' => '@backend/views/option/index'
    ]
],
```

- in `remote/config/main.php`

```php
'bootstrap' => [
    'log',
    'option'
],

'components' => [
    'option' => [
        'class' => \backend\models\option\Option::class
    ]
],
```

- in `frontend/config/main.php`

```php
'bootstrap' => [
    'log',
    'option',
    'maintenanceMode'
],

'components' => [
    'option' => [
        'class' => \backend\models\option\Option::class
    ]
],
```

Access to this settings:

```php
// application name
\Yii::$app->get('option')->get('app_name');
```


Maintenance Mode
----------------

Component has additional options [here](https://github.com/brussens/yii2-maintenance-mode#options).

Custom layout at `vendor/acid23m/yii2-admin/src/views/layouts/maintenance.php`.
Custom view at `vendor/acid23m/yii2-admin/src/views/maintenance-mode/index.php`.


User scripts
------------

Add script widgets in the main layout.

- head scripts

```html
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <?= \dashboard\widgets\HeadScript::widget() ?>

    <?php $this->head() ?>
</head>
```

- bottom body scripts and metrics

```html
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?= \dashboard\widgets\BodyScript::widget() ?>
<?= \dashboard\widgets\Metrica::widget() ?>
<?php $this->endBody() ?>
</body>
```
