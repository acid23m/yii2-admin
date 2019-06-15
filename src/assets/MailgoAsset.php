<?php

namespace dashboard\assets;

use yii\web\AssetBundle;

/**
 * Dialog for mailto links.
 *
 * @package dashboard\assets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 * @see https://mailgo.js.org/
 */
final class MailgoAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $js = [
        'https://unpkg.com/mailgo@0.6.7/dist/mailgo.min.js'
    ];

}
