<?php

namespace dashboard\models\trash;

/**
 * Recycle bin functions.
 *
 * @package dashboard\models\trash
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
interface TrashableInterface
{
    /**
     * Attribute name which marks that item was deleted.
     * @return string
     */
    public static function getDeleteAttribute(): string;

    /**
     * Trash group name.
     * @return string
     */
    public function getOwnerLabel(): string;

    /**
     * Trash model title.
     * @return string
     */
    public function getItemLabel(): string;

    /**
     * Url to view item.
     * @return string
     */
    public function getViewUrl(): string;

}
