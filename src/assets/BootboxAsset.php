<?php

namespace dashboard\assets;

use yii\web\AssetBundle;

/**
 * Bootbox as confirm dialog.
 *
 * @package dashboard\assets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 * @see http://bootboxjs.com/
 */
final class BootboxAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bower/bootbox';
    /**
     * {@inheritdoc}
     */
    public $js = [
        'bootbox.js'
    ];

    /**
     * Overrides yii confirm.
     */
    public static function overrideSystemConfirm(): void
    {
        $lang = \Yii::$app->language;
        $confirm = <<<JS
yii.confirm = function (message, ok, cancel) {
    bootbox.setDefaults({
        locale: '$lang'
    });

    bootbox.confirm(message, result => {
        if (result) {
            const loadingBlock = jQuery('#loading-block');
            if (loadingBlock) {
                loadingBlock.fadeIn('fast');
                setTimeout(() => loadingBlock.fadeOut('fast'), 5000);
            }

            !ok || ok();
        } else {
            !cancel || cancel();
        }
    });
};
JS;

        \Yii::$app->getView()->registerJs($confirm);
    }

}
