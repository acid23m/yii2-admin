<?php

namespace dashboard\traits;

use yii\base\InvalidArgumentException;

/**
 * Model helpers.
 *
 * @package dashboard\traits
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
trait Model
{
    /**
     * Selection list.
     * @param string $_list
     * @return \Closure
     * @throws InvalidArgumentException
     */
    public function getList(string $_list): \Closure
    {
        if (!\property_exists($this, $_list)) {
            throw new InvalidArgumentException('List not found.');
        }

        $list = $this->$_list;

        if (!\is_iterable($list)) {
            throw new InvalidArgumentException('List must be an associative array.');
        }

        /**
         * @param bool $associative
         * @return iterable
         */
        return static function (bool $associative = true) use ($list): iterable {
            return $associative ? $list : \array_keys($list);
        };
    }

    /**
     * Shows some properties in one string.
     * @param string $template {property} will be replaced
     * @return string
     */
    public function asString(string $template): string
    {
        \preg_match_all('/{([\w_]+)}/', $template, $matches);

        $search = $matches[0];
        $replace = function () use ($matches) {
            foreach ($matches[1] as $property) {
                try {
                    yield $this->$property;
                } catch (\Throwable $e) {
                    \Yii::debug($e->getMessage(), __CLASS__);
                }
            }
        };

        return \str_replace($search, \iterator_to_array($replace()), $template);
    }

}
