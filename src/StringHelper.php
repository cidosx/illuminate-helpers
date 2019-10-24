<?php

namespace Zeigo\Illuminate\Helpers;

use Illuminate\Support\Str;
use UnexpectedValueException;

class StringHelper extends Str
{
    /**
     * The cache of string sets.
     * @var array
     */
    protected static $insetCache = [];

    /**
     * Generate a more truly "random" string
     *
     * @param string $type
     * @param int $len
     * @return string
     */
    public static function randomString(string $type = 'distinct_alnum', $len = 8): string
    {
        switch ($type) {
            case 'distinct_alnum':
                $pool = '3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKLMNPQRSTUVWXY';
                break;
            case 'numeric':
                return str_pad(mt_rand(0, str_repeat(9, $len)), $len, '0', STR_PAD_LEFT);
            case 'alnum':
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'nozero':
                $pool = '123456789';
                break;
            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'distinct':
                $pool = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
                break;
            case 'md5':
                return md5(uniqid(mt_rand()));
            case 'sha1':
                return sha1(uniqid(mt_rand(), true));

            default:
                throw new UnexpectedValueException('random type error');
        }

        return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
    }

    /**
     * Determine if a given string in a regular string sets.
     *
     * @param string $needle
     * @param string $haystack
     * @param string $separator
     * @return bool
     */
    public static function inSet(string $needle, string $haystack, string $separator = ','): bool
    {
        if (empty($haystack)) {
            return $haystack === $needle;
        }

        if (! isset(static::$insetCache[$contentHash = sha1($haystack)])) {
            static::$insetCache[$contentHash] = array_flip(explode($separator, $haystack));
        }

        return isset(static::$insetCache[$contentHash][$needle]);
    }
}
