<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.08.18
 * Time: 2:45
 */

namespace dashboard\widgets;

use yii\base\Widget;

/**
 * Render metrics.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Metrica extends Widget
{
    /**
     * Get content from file.
     * @return string
     */
    public function run(): string
    {
        // bypass on development
        if (YII_DEBUG) {
            return '';
        }

        $model = new \dashboard\models\option\Metrica;

        // google analytics
        $this->view->registerLinkTag([
            'rel' => 'dns-prefetch',
            'href' => '//www.googletagmanager.com'
        ]);

        $google_analytics = '';
        $google_analytics_id = $model->get('google_analytics');
        if (!empty($google_analytics_id)) {
            $google_analytics = <<<GA
<script async src="https://www.googletagmanager.com/gtag/js?id={$google_analytics_id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{$google_analytics_id}');
</script>

GA;
        }

        // yandex metrica
        $this->view->registerLinkTag([
            'rel' => 'dns-prefetch',
            'href' => '//mc.yandex.ru'
        ]);

        $yandex_metrika = '';
        $yandex_metrika_id = $model->get('yandex_metrika');
        if (!empty($yandex_metrika_id)) {
            $yandex_metrika = <<<YM
<script>
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter{$yandex_metrika_id} = new Ya.Metrika({
                    id: {$yandex_metrika_id},
                    webvisor: true,
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true
                });
            } catch (e) {
            }
        });
        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () {
                n.parentNode.insertBefore(s, n);
            };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";
        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript>
    <div><img src="//mc.yandex.ru/watch/{$yandex_metrika_id}" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
YM;
        }

        return $google_analytics . $yandex_metrika;
    }

}
