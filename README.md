Admin
=====
Admin module for my dockerized Yii2 application.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist acid23m/yii2-admin "~1.0"
```

or add

```
"acid23m/yii2-admin": "~1.0"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, do next:

Add component in `common/config/main.php`.

```php
'modules' => [
    'datecontrol' => [
        'class' => \kartik\datecontrol\Module::class,
        'displaySettings' => [
            \kartik\datecontrol\Module::FORMAT_DATETIME => 'dd.MM.yyyy hh:mm:ss a',
            \kartik\datecontrol\Module::FORMAT_DATE => 'dd.MM.yyyy',
            \kartik\datecontrol\Module::FORMAT_TIME => 'hh:mm:ss a'
        ],
        'saveSettings' => [
            \kartik\datecontrol\Module::FORMAT_DATETIME => 'php:' . STANDARD_DATETIME_FORMAT,
            \kartik\datecontrol\Module::FORMAT_DATE => 'php:' . STANDARD_DATE_FORMAT,
            \kartik\datecontrol\Module::FORMAT_TIME => 'php:H:i:s'
        ],
        'saveTimezone' => 'UTC',
        'autoWidgetSettings' => [
            \kartik\datecontrol\Module::FORMAT_DATETIME => [
                'type' => DateTimePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayBtn' => true,
                    'todayHighlight' => true
                ]
            ],
            \kartik\datecontrol\Module::FORMAT_DATE => [
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayBtn' => true,
                    'todayHighlight' => true
                ]
            ],
            \kartik\datecontrol\Module::FORMAT_TIME => [
                'addon' => '',
                'pluginOptions' => [
                    'minuteStep' => 1,
                    'secondStep' => 5,
                    'showMeridian' => false
                ]
            ]
        ]
    ]
],

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

Add module in `backend/config/main.php`.

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
        'left_menu' => '@backend/config/left_menu.php',
        'top_menu' => '@backend/config/top_menu.php',
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
            'class' => \common\models\sitemap\Sitemap::class // must implement \dashboard\models\sitemap\SitemapCollectionInterface
        ],
        // search index config
        'search_items' => [
            'class' => \common\models\search\SearchCollection::class // must implement \dashboard\models\index\SearchCollectionInterface
        ]
    ]
],

'components' => [
    'option' => [
        'class' => \dashboard\models\option\web\Main::class, // original class
        //'class' => \common\models\option\Option::class, // own class must be extended from \dashboard\models\option\web\Main
        //'view' => '@backend/views/option/index', // example in vendor/acid23m/yii2-admin/src/views/option-main/index.php
    ]
],
```

Add module in `console/config/main.php`.

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
            'class' => \common\models\sitemap\SitemapCollection::class // must implement \dashboard\models\sitemap\SitemapCollectionInterface
        ],
        // search index config
        'search_items' => [
            'class' => \common\models\search\SearchCollection::class // must implement \dashboard\models\index\SearchCollectionInterface
        ]
    ]
],
```

Add module in `remote/config/main.php`.

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

Add components in `frontend/config/main`.

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
        //'class' => \common\models\option\Option::class // must be extended from \dashboard\models\option\web\Main
    ],
    'searchIndex' => [
        'class' => \dashboard\models\index\SearchIndex::class
    ],
],
```

Every controller in backend must be extended from `\dashboard\controllers\web\BaseController`.

Add rule to UrlManager config.

```php
'rules' => [
    '/' => '/dashboard/home/index'
]
```

Rest Api examples in *html* directory.


Menu
----

**Left menu:**

Create file somewhere, for example `backend/config/left_menu.php`.
Configure module to use it.

```php
'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'left_menu' => '@backend/config/left_menu.php',
    ]
]
```

File must return an array.

```php
return [
    'title string' => [ // section header
        [
            'label' => 'Menu Item', // menu item label
            'url' => '#', // url compatible with Url::to()
            'icon' => 'th', // fontawesome icon id
            'badge' => '123', // badge text
            'badgeOptions' => ['class' => 'label-success'] // badge config
        ]
    ]
]
```

**Top menu:**

Create file somewhere, for example `backend/config/top_menu.php`.
Configure module to use it.

```php
'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'top_menu' => '@backend/config/top_menu.php',
    ]
]
```

File must return an array.

```php
return [
    [
        'label' => 'Menu Item', // menu item label
        'url' => '#', // url compatible with Url::to()
        'icon' => 'th', // fontawesome icon id
        'badge' => '123', // badge text
        'badgeOptions' => ['class' => 'bg-green'] // badge config
    ],
    [
        'icon' => 'circle-o',
        'items' => [
            [
                'label' => 'Subitem',
                'badge' => 'str',
                'badgeOptions' => ['class' => 'bg-red']
            ],
            [
                'label' => 'Site',
                'url' => '/'
            ]
        ]
    ]
]
```


App options
-----------

Application options locate in `common/data/.app.ini` file.

If you want to add additional options, you must create class and extend it from

- `\dashboard\models\option\web\Main` for backend and frontend

```php
namespace common\models\option;

use yii\helpers\ArrayHelper;

/**
 * Extended application options.
 *
 * @property string $app_lang
 * @property string $service_api_key
 *
 * @package common\models\option
 */
final class Option extends \dashboard\models\option\web\Main
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
        'class' => \common\models\option\Option::class,
        'view' => '@backend/views/option/index.php'
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
        'class' => \remote\models\option\Option::class
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
        'class' => \common\models\option\Option::class
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


Sitemap
-------

You can create sitemap XML files for web crawlers.

First create class that will collect items for sitemap.

```php
namespace common\models\sitemap;

use common\models\post\PostRecord;
use dashboard\models\sitemap\SitemapConfigInterface;
use samdark\sitemap\Sitemap;
use yii\web\UrlManager;

/**
 * Class SitemapCollection.
 *
 * @package common\models\sitemap
 */
final class SitemapCollection implements SitemapCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function dynamicItems(Sitemap $sitemap): void
    {
        /** @var UrlManager $url_manager */
        $url_manager = \Yii::$app->get('urlManagerFrontend');

        // posts
        $posts_query = PostRecord::find()->actual()->published()->ordered();
        foreach ($posts_query->batch(50) as $posts) {
            /** @var PostRecord $post */
            foreach ($posts as &$post) {
                $sitemap->addItem(
                    $url_manager->createAbsoluteUrl(['post/view', 'slug' => $post->slug]),
                    (new \DateTime($post->updated_at, new \DateTimeZone(\Yii::$app->timeZone)))->format('U'),
                    Sitemap::MONTHLY,
                    0.3
                );
            }
        }
        unset($posts_query, $post);
    }

    /**
     * {@inheritdoc}
     */
    public function staticItems(Sitemap $sitemap): void
    {
        /** @var UrlManager $url_manager */
        $url_manager = \Yii::$app->get('urlManagerFrontend');
        $base_url = rtrim($url_manager->getHostInfo(), '/');

        // home page
        $sitemap->addItem($base_url);
    }

}
```

Next define it in module configuration in `backend/config/main.php`
and `console/config/main.php`.

```php
'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'sitemap_items' => [
            'class' => \common\models\sitemap\SitemapCollection::class
        ]
    ]
],
```

Than run index manually from the "Service section" of the admin. panel
or by cron task with command `php /app/yii dashboard/sitemap/index`.


Search Index
------------

You can use full text search.

*REQUIREMENTS*: Database MySQL/MariaDB must be installed
and configured with `S_DB_NAME`, `S_DB_USER` and `S_DB_PASSWORD`
environment variables from `.env` file.

*RECOMMENDS*: install [php-ds extension](https://github.com/php-ds).

First create class that will collect items for search index.

```php
namespace common\models\search;

use common\models\post\PostRecord;
use dashboard\components\index\SearchCollectionInterface;
use S2\Rose\Entity\Indexable;
use S2\Rose\Indexer;
use yii\helpers\StringHelper;
use yii\web\UrlManager;

/**
 * Search index collection.
 *
 * @package common\models\search
 */
class SearchCollection implements SearchCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function index(Indexer $indexer): void
    {
        /** @var UrlManager $url_manager */
        $url_manager = \Yii::$app->get('urlManagerFrontend');

        // posts
        $posts_query = PostRecord::find()->actual()->published()->ordered();
        foreach ($posts_query->batch(50) as $posts) {
            /** @var PostRecord $post */
            foreach ($posts as &$post) {
                $item = new Indexable(
                    'post-' . $post->id,
                    $post->title,
                    $post->description
                );
                $item->setDescription(
                    StringHelper::truncateWords($post->description, 100)
                );
                $item->setDate(
                    new \DateTime($post->updated_at, new \DateTimeZone(\Yii::$app->timeZone))
                );
                $item->setUrl(
                    $url_manager->createAbsoluteUrl(['post/view', 'slug' => $post->slug])
                );

                $indexer->index($item);
            }
        }
        unset($posts_query, $post, $item);
    }

}
```

Next define it in module configuration in `backend/config/main.php`
and `console/config/main.php`.

```php
'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'search_items' => [
            'class' => \common\models\search\SearchCollection::class
        ]
    ]
],
```

Than run index manually from the "Search Index section" of the admin. panel
or by cron task with command `php /app/yii dashboard/search/index`.

To use search index in front configure component in `frontend/config/main.php`.

```php
'components' => [
    'searchIndex' => [
        'class' => \dashboard\models\index\SearchIndex::class
    ],
],
```

Create controller and point form with search input to it.

```php
namespace frontend\controllers;

use dashboard\components\index\SearchIndex;
use yii\helpers\Html;
use yii\web\Controller;

/**
 * Class SearchController.
 *
 * @package frontend\controllers
 */
final class SearchController extends Controller
{
    /**
     * Find contents by query.
     * @param string $q Search query
     * @return string
     */
    public function actionResult($q): string
    {
        if (!empty($q)) {
            $q = Html::encode($q);

            /** @var SearchIndex $search_index */
            $search_index = \Yii::$app->get('searchIndex');
            $results = $search_index->find($q);
        } else {
            $results = [];
        }

        return $this->render('result', compact('q', 'results'));
    }
}
```

Finally create view file for search results.


Multi-Page
----------

You can manage content on page that depends on URL query or Geo data.
Just install [multipage](https://github.com/acid23m/yii2-multipage) extension

```
"require": {
  "acid23m/yii2-multipage": "dev-master"
},
"repositories": [
  {
    "type": "git",
    "url": "git@github.com:acid23m/yii2-multipage.git"
  }
]
```

and add module in `frontend/config/main.php`.

```php
'modules' => [
    \multipage\Module::DEFAULT_ID => [
        'class' => \multipage\Module::class,
        'controllerNamespace' => 'multipage\controllers'
    ],
],
```


Datetime control
----------------

System (os, db) timezone is always UTC.
The [Date Control](https://github.com/kartik-v/yii2-datecontrol) module allows controlling date formats of attributes separately for View and Model.

Once the `datecontrol` module is configured, you can use widgets for forms.
Widget show datetime/time in application timezone, but save it in UTC.

```php
<?= $form->field($model, 'datetime')->widget(\kartik\datecontrol\DateControl::class, [
    'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
    'displayTimezone' => \Yii::$app->getTimeZone()
]) ?>
```

To show saved datetime/time you can convert it, e.g:

```php
<?= \yii\widgets\DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [
            'attribute' => 'datetime',
            'format' => 'datetime',
            'value' => function ($model, $widget) {
                $dt = new \DateTime($model->datetime, new \DateTimeZone('UTC'));
                $dt->setTimezone(new \DateTimeZone(\Yii::$app->getTimeZone()));

                return $dt;
            }
        ],
        [
            'attribute' => 'title',
            'format' => 'html',
            'value' => Html::tag('strong', $model->title)
        ],
        'description:raw',
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'value' => function ($model, $widget) {
                $dt = new \DateTime($model->created_at, new \DateTimeZone('UTC'));
                $dt->setTimezone(new \DateTimeZone(\Yii::$app->getTimeZone()));

                return $dt;
            }
        ],
        [
            'attribute' => 'updated_at',
            'format' => 'datetime',
            'value' => function ($model, $widget) {
                $dt = new \DateTime($model->updated_at, new \DateTimeZone('UTC'));
                $dt->setTimezone(new \DateTimeZone(\Yii::$app->getTimeZone()));

                return $dt;
            }
        ]
    ]
]) ?>
```

---

*Developed for internal usage.*
