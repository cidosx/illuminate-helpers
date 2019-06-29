<?php

namespace Zeigo\Illuminate\Helpers;

/**
 * 文件辅助函数
 */
class FileHelper
{
    /**
     * 创建文件夹
     *
     * @param   string  $path
     * @param   bool  $parents
     * @param   bool  $loose
     * @return  bool
     */
    public static function mkDir(string $path, bool $parents = true, bool $loose = false): bool
    {
        ////////////////////////////////////////////////////////
        // mkdir 前后两次判断, 避免多进程同时创建文件夹时的报错
        //
        // 参数说明:
        //     parents - 默认使用 mkdir -p 方式
        //     loose - false 为严格模式, 文件夹默认 mode 755
        ////////////////////////////////////////////////////////

        if (! is_dir($path) && false === @mkdir($path, $loose ? 0777 : 0755, $parents) && ! is_dir($path)) {
            return false;
        }

        return true;
    }
}
