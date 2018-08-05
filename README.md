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

- Add module in *backend/config/main.php*.

```php
'bootstrap' => [
    'log',
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
        // extend options
        'option_model' => \backend\modules\option\models\Option::class, // must be extended from \dashboard\models\option\Main
        'option_view' => '@backend/modules/option/views/option'
    ]
],
```

- Add module in *console/config/main.php*.

```php
'bootstrap' => [
    'log',
    'dashboard'
],

'modules' => [
    'dashboard' => [
        'class' => dashboard\Module::class,
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
        }
    ]
],
```

- Add module in *remote/config/main.php*.

```php
'bootstrap' => [
    'log',
    'dashboard'
],

'modules' => [
    'dashboard' => [
        'class' => dashboard\Module::class,
        'controllerNamespace' => 'dashboard\controllers\rest',
        // extend options
        'option_model' => \remote\modules\v1\modules\option\models\Option::class // must be extended from \dashboard\models\option\Main
    ]
],
```

- Every controller in backend must be extended from *\dashboard\controllers\web\BaseController*.

- Add rule to UrlManager config.

```php
'rules' => [
    '/' => '/dashboard/home/index'
]
```

Rest Api examples in *html* directory.
