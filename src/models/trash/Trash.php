<?php

namespace dashboard\models\trash;

use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;

/**
 * Recycle bin.
 *
 * @package dashboard\models\trash
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Trash
{
    /**
     * Gets all trash items.
     * @return ArrayDataProvider
     * @throws InvalidConfigException
     */
    public static function getItems(): ArrayDataProvider
    {
        $module = \dashboard\Module::getInstance();
        if ($module === null) {
            throw new InvalidConfigException('\dashboard\Module not defined.');
        }

        $items = [];
        $trash_items = $module->trash_items;
        foreach ($trash_items as $trash_item) {
            /** @var ActiveRecord|TrashableInterface $trash_item */
            try {
                /** @var ActiveRecord[]|TrashableInterface[] $_items */
                $_items = $trash_item::find()
                    ->where([$trash_item::getDeleteAttribute() => true])
                    ->all();

                foreach ($_items as $item) {
                    $items[] = [
                        'model' => $item,
                        'updated_at' => $item->updated_at ?? null
                    ];
                }
            } catch (\Throwable $e) {
            }
        }

        return new ArrayDataProvider([
            'allModels' => $items,
            'sort' => [
                'attributes' => ['updated_at'],
                'defaultOrder' => ['updated_at' => SORT_DESC]
            ]
        ]);
    }

    /**
     * Gets number of items in the trash.
     * @return int
     * @throws InvalidConfigException
     */
    public static function getCount(): int
    {
        $module = \dashboard\Module::getInstance();
        if ($module === null) {
            throw new InvalidConfigException('\dashboard\Module not defined.');
        }

        $count = 0;
        $trash_items = $module->trash_items;
        foreach ($trash_items as $trash_item) {
            /** @var ActiveRecord|TrashableInterface $trash_item */
            try {
                $_count = $trash_item::find()
                    ->where([$trash_item::getDeleteAttribute() => true])
                    ->count('id');

                $count += (int) $_count;
            } catch (\Throwable $e) {
            }
        }

        return $count;
    }

}
