Admin
=====
Admin module for my dockerized Yii2 application.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
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

- add module in *backend/config/main.php*.

```php
'bootstrap' => [
    'log',
    'dashboard'
],

'modules' => [
    'dashboard' => [
        'class' => \dashboard\Module::class,
        'left_menu' => [
            'section' => [ // section header
                [
                    'label' => 'Menu Item', // menu item label
                    'url' => '#', // url compatible with Url::to()
                    'icon' => 'th' // fontawesome icon id
                ]
            ]
        ]
    ]
],
```

- every controller in backend must be extended from *\dashboard\controllers\BaseController*.
- add rule to UrlManager config

```php
'rules' => [
    '/' => '/dashboard/home/index'
]
``
