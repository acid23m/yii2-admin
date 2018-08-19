<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 07.08.18
 * Time: 2:21
 */

namespace dashboard\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yiister\gentelella\assets\Asset as GentelellaAsset;

/**
 * Class AppAsset.
 *
 * @package dashboard\assets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class AppAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@vendor/acid23m/yii2-admin/src/assets';
    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/site.css'
    ];
    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/site.js'
    ];
    /**
     * {@inheritdoc}
     */
    public $depends = [
        YiiAsset::class,
        GentelellaAsset::class,
        BootboxAsset::class
    ];

}
