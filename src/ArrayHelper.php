<?php

namespace Zeigo\Illuminate\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * 数组辅助函数
 */
class ArrayHelper extends Arr
{
    /**
     * 性能高于 array_unique 的一维数组去重
     *
     * @param   array  $data
     * @return  array
     */
    public static function unique(array $data): array
    {
        return array_keys(array_flip($data));
    }

    /**
     * 批量执行 array_pluck
     *
     * @param   \ArrayAccess|array  $array
     * @param   array  $keys
     * @return  array
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
     * pluck 同时去重
     *
     * @param   \ArrayAccess|array  $array
     * @param   string  $key
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

            // 相同值仅set一次
            isset($result[$value]) || $result[$value] = true;
        }

        return array_keys($result);
    }

    /**
     * 数组下标转驼峰
     *     - 依赖框架 helper: Str::camel
     *     - 默认不递归, 便于 array_map
     *
     * @param   mixed  $data
     * @param   bool  $recursive
     * @return  mixed
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
