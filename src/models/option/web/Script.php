<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.08.18
 * Time: 1:45
 */

namespace dashboard\models\option\web;

/**
 * Class Script.
 *
 * @package dashboard\models\option\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Script extends \dashboard\models\option\Script
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'head_script' => \Yii::t('dashboard', 'razdel head'),
            'body_script' => \Yii::t('dashboard', 'razdel body')
        ];
    }

}
