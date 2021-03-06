<?php

namespace dashboard\widgets;

use yii\base\Widget;

/**
 * Renders metrics.
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Metrica extends Widget
{
    /**
     * Gets content from file.
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
        $this->getView()->registerLinkTag([
            'rel' => 'dns-prefetch',
            'href' => '//www.googletagmanager.com'
        ]);

        $google_analytics = '';
        $google_analytics_id = $model->get('google_analytics');
        if ($google_analytics_id !== null && $google_analytics_id !== '') {
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
        $this->getView()->registerLinkTag([
            'rel' => 'dns-prefetch',
            'href' => '//mc.yandex.ru'
        ]);

        $yandex_metrika = '';
        $yandex_metrika_id = $model->get('yandex_metrika');
        if ($yandex_metrika_id !== null && $yandex_metrika_id !== '') {
            $yandex_metrika = <<<YM
<script>
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter{$yandex_metrika_id} = new Ya.Metrika2({
                    id: {$yandex_metrika_id},
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    webvisor: true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/tag.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks2");
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/{$yandex_metrika_id}" style="position:absolute; left:-9999px;" alt="" /></div>
</noscript>
YM;
        }

        return $google_analytics . $yandex_metrika;
    }

}
