<?php

namespace Zeigo\Illuminate\Helpers;

class FileHelper
{
    /**
     * Make dir whenever possible.
     *
     * @param string $pathname
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public static function mkdir(string $pathname, int $mode = 0755, bool $recursive = true): bool
    {
        if (! is_dir($pathname) && false === @mkdir($pathname, $mode, $recursive) && ! is_dir($pathname)) {
            return false;
        }

        return true;
    }
}
