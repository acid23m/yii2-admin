<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.08.18
 * Time: 1:02
 */

namespace dashboard\models\option\web;

/**
 * Class Metrika.
 *
 * @package dashboard\models\option\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Metrica extends \dashboard\models\option\Metrica
{
    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'google_analytics' => \Yii::t('dashboard', 'gugl analitika'),
            'yandex_metrika' => \Yii::t('dashboard', 'ya metrika')
        ];
    }

}
