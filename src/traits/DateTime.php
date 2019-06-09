<?php

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
     * @static
     * @return string
     * @throws \Exception
     */
    public static function getNow(): string
    {
        $now = new \DateTime('now', new \DateTimeZone(\Yii::$app->getTimeZone()));

        return $now->format(STANDARD_DATETIME_FORMAT);
    }

    /**
     * Current time in UTC.
     * @static
     * @return string
     * @throws \Exception
     */
    public static function getNowUTC(): string
    {
        $now = new \DateTime('now', new \DateTimeZone(\Yii::$app->getTimeZone()));
        $now->setTimezone(new \DateTimeZone('UTC'));

        return $now->format(STANDARD_DATETIME_FORMAT);
    }

    /**
     * Converts time to local timezone.
     * @static
     * @param string $t Time
     * @param string $from_tz Timezone
     * @return \DateTime
     * @throws \Exception
     */
    public static function toLocalTimezone(string $t, string $from_tz = 'UTC'): \DateTime
    {
        $dt = new \DateTime($t, new \DateTimeZone($from_tz));
        $dt->setTimezone(new \DateTimeZone(\Yii::$app->getTimeZone()));

        return $dt;
    }

}
