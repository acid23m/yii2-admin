<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 11.09.17
 * Time: 15:27
 */

namespace dashboard\traits;

/**
 * Date and time utilities.
 *
 * @package dashboard\traits
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
trait DateTime
{
    /**
     * Current time in current timezone.
     * @return string
     */
    public static function getNow(): string
    {
        $now = new \DateTime('now', new \DateTimeZone(\Yii::$app->getTimeZone()));

        return $now->format(STANDARD_DATETIME_FORMAT);
    }

    /**
     * Current time in UTC.
     * @return string
     */
    public static function getNowUTC(): string
    {
        $now = new \DateTime('now', new \DateTimeZone(\Yii::$app->getTimeZone()));
        $now->setTimezone(new \DateTimeZone('UTC'));

        return $now->format(STANDARD_DATETIME_FORMAT);
    }

}
