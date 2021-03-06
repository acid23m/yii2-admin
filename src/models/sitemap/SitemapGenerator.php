<?php

namespace dashboard\models\sitemap;

use samdark\sitemap\Index;
use samdark\sitemap\Sitemap;
use yii\helpers\FileHelper;
use yii\web\UrlManager;

/**
 * Generates sitemaps from configuration.
 *
 * @package dashboard\models\sitemap
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class SitemapGenerator
{
    /**
     * Creates sitemap files.
     * @throws \InvalidArgumentException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidArgumentException
     * @throws \yii\base\InvalidConfigException
     */
    public static function write(): void
    {
        $dir = \rtrim(\Yii::getAlias('@frontend/web/sitemap'), '/');
        FileHelper::createDirectory($dir);

        // remove previous files
        \passthru("rm -f $dir/sitemap*.xml");

        /** @var UrlManager $url_manager */
        $url_manager = \Yii::$app->get('urlManagerFrontend');
        $base_url = \rtrim($url_manager->getHostInfo(), '/');

        $module = \dashboard\Module::getInstance();
        if ($module !== null && isset($module->sitemap_items['class'])) {
            /** @var null|SitemapCollectionInterface $sitemap_config */
            try {
                $sitemap_config = new $module->sitemap_items['class'];
            } catch (\Throwable $e) {
                $sitemap_config = null;
            }

            if ($sitemap_config !== null) {
                // static pages
                $static_sitemap = new Sitemap($dir . '/sitemap_static.xml');
                $sitemap_config->staticItems($static_sitemap);
                $static_sitemap->write();
                $static_sitemap_items = $static_sitemap->getSitemapUrls("$base_url/sitemap/");
                unset($static_sitemap);

                // dynamic pages
                $dynamic_sitemap = new Sitemap($dir . '/sitemap_dynamic.xml');
                $sitemap_config->dynamicItems($dynamic_sitemap);
                $dynamic_sitemap->write();
                $dynamic_sitemap_items = $dynamic_sitemap->getSitemapUrls("$base_url/sitemap/");
                unset($dynamic_sitemap);

                // index file
                $index_sitemap = new Index($dir . '/sitemap.xml');
                foreach ($static_sitemap_items as $static_sitemap_item) {
                    $index_sitemap->addSitemap($static_sitemap_item);
                }
                foreach ($dynamic_sitemap_items as $dynamic_sitemap_item) {
                    $index_sitemap->addSitemap($dynamic_sitemap_item);
                }
                $index_sitemap->write();
            }
        }
    }

}
