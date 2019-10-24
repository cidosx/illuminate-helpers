<?php

namespace Zeigo\Illuminate\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ArrayHelper extends Arr
{
    /**
     * Removes duplicate values from a one-dimensional array.
     *
     * @param array $data
     * @return array
     */
    public static function unique(array $data): array
    {
        return array_keys(array_flip($data));
    }

    /**
     * Pluck more array of values from more array.
     *
     * @param \Traversable|array $array
     * @param array $keys
     * @return array
     */
    public static function pluckBatch($array, array $keys): array
    {
        $result = array_fill_keys($keys, []);

        foreach ($array as $value) {
            foreach ($keys as $key) {
                if (static::has($value, $key)) {
                    $result[$key][] = static::get($value, $key);
                }
            }
        }

        return $result;
    }

    /**
     * Pluck an array of values from an array and removes duplicate values.
     *
     * @param \Traversable|array $array
     * @param string $key
     * @return  array
     */
    public static function pluckUnique($array, string $key): array
    {
        $result = [];

        foreach ($array as $item) {
            if (! static::has($item, $key)) {
                continue;
            }

            $value = static::get($item, $key);

            isset($result[$value]) || $result[$value] = true;
        }

        return array_keys($result);
    }

    /**
     * Convert all of array index to camel case.
     *
     * @param mixed $data
     * @param bool $recursive
     * @return mixed
     */
    public static function camelIndex($data, bool $recursive = false)
    {
        if (! is_array($data)) {
            return $data;
        }

        $result = [];

        foreach (array_keys($data) as $key) {
            $result[is_numeric($key) ? $key : Str::camel($key)] = $recursive ? self::camelIndex($data[$key], $recursive) : $data[$key];
        }

        return $result;
    }
}
