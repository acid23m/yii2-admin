<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 15.08.18
 * Time: 1:16
 */

namespace dashboard\models\sitemap;

use samdark\sitemap\Sitemap;

/**
 * Configuration for sitemaps.
 *
 * @package dashboard\models\sitemap
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 * @link https://github.com/samdark/sitemap
 */
interface SitemapCollectionInterface
{
    /**
     * Add dynamic pages.
     * $sitemap->addItem('http://example.com/mylink4', time(), Sitemap::DAILY, 0.3);
     * $sitemap->addItem([
     *     'ru' => 'http://example.com/ru/mylink4',
     *     'en' => 'http://example.com/en/mylink4',
     * ], time(), Sitemap::DAILY, 0.3);
     *
     * @param Sitemap $sitemap
     */
    public function dynamicItems(Sitemap $sitemap): void;

    /**
     * Add static pages.
     * $sitemap->addItem('http://example.com/about');
     *
     * @param Sitemap $sitemap
     */
    public function staticItems(Sitemap $sitemap): void;

}
