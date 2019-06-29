<?php

namespace Zeigo\Illuminate\Helpers;

use InvalidArgumentException;
use Illuminate\Support\Str;

/**
 * 处理csv的辅助函数
 */
class CsvHelper
{
    /** end of line */
    const EOL = "\r\n";

    /** 字符定界 */
    const ENCLOSURE = '"';

    /** 默认逗号分隔符 */
    const DELIMITER = ',';

    /**
     * 保存为csv文件
     *
     * @param   string  $filePathName
     * @param   array  $data
     * @param   bool  $overwrite
     * @return  string
     */
    public static function store(string $filePathName, array $data, bool $overwrite = false): string
    {
        if (strrchr($filePathName, '.csv') !== '.csv') {
            throw new InvalidArgumentException('The file extension must be .csv');
        }

        if (empty($data['rows'])) {
            throw new InvalidArgumentException('Missing required data for rows.');
        }

        if (is_file($filePathName) && !$overwrite) {
            throw new InvalidArgumentException('File already exists.');
        }

        $resource = fopen($filePathName, 'w');

        // 添加bom头
        // EFBBBF 为 utf8 bom
        fwrite($resource, pack('H*', 'EFBBBF'));

        self::writeFile($resource, $data);

        fclose($resource);
        return self::resolveProjectRealPath($filePathName);
    }

    /**
     * 以追加方式写文件 (不检查文件后缀)
     *
     * @param   string  $filePathName
     * @param   array  $data
     * @return  string
     */
    public static function append(string $filePathName, array $data): string
    {
        if (empty($data['rows'])) {
            throw new InvalidArgumentException('Missing required data for rows.');
        }

        if (!is_file($filePathName)) {
            throw new InvalidArgumentException('File not exists.');
        }

        $resource = fopen($filePathName, 'a');

        self::writeFile($resource, $data);

        fclose($resource);
        return self::resolveProjectRealPath($filePathName);
    }

    /**
     * 下载文件
     *
     * @param   string  $filename
     * @param   array  $data
     * @return  void
     */
    public static function download(string $filename, array $data)
    {
        // 类似的逻辑, 但结果变为输出到浏览器

        if (empty($data['rows'])) {
            throw new InvalidArgumentException('Missing required data for rows.');
        }

        // 自动加上文件后缀
        $filename = trim($filename);
        if (strrchr($filename, '.csv') !== '.csv') {
            $filename .= '.csv';
        }

        ob_end_clean();
        ob_start();

        header('Content-Type:text/csv;charset=utf8');
        header('Content-Disposition:attachment;filename=' . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:Mon, 26 Jul 1997 05:00:00 GMT');
        header('Pragma:public');

        // 添加bom头
        // EFBBBF 为 utf8 bom
        echo pack('H*', 'EFBBBF');

        // 自定义一个数组header, 与cell分开处理
        if (!empty($data['header'])) {
            echo self::ENCLOSURE
            . join(self::ENCLOSURE . self::DELIMITER . self::ENCLOSURE, $data['header'])
            . self::ENCLOSURE
            . self::EOL;
        }

        // cell中需要判断是否为长数字
        foreach ($data['rows'] as $row) {
            if (is_array($row)) {
                echo rtrim(self::formatRows($row), self::DELIMITER) . self::EOL;
            } else {
                echo $row . self::EOL;
            }
        }

        // 输出缓冲区的内容到浏览器
        ob_end_flush();

        exit;
    }

    /** TODO: 预留的方法, 返回项目目录的绝对路径, 之后完成时需要考虑软连接情况 */
    private static function resolveProjectRealPath(string $path): string
    {
        return $path;
    }

    /**
     * 格式化每行的数据
     *
     * @param   array  $rowData
     * @return  string
     */
    private static function formatRows(array $rowData): string
    {
        $contents = '';

        foreach ($rowData as $idx => $cell) {
            if (is_numeric($cell)) {
                // 使用特殊下标来处理需要格式化的单元格
                if (Str::startsWith($idx, 'CELL_FMT_')) {
                    /**
                     * csv 数字格式 -> ="00001"
                     */
                    $contents .= '=' . self::ENCLOSURE . $cell . self::ENCLOSURE . self::DELIMITER;
                } else {
                    // 不处理此类数据, 一般为金额数量等需要计算的
                    $contents .= self::ENCLOSURE . $cell . self::ENCLOSURE . self::DELIMITER;
                }
            } else {
                // 转义内容中的引号
                $contents .= self::ENCLOSURE . str_replace(
                    self::ENCLOSURE,
                    self::ENCLOSURE . self::ENCLOSURE,
                    $cell
                ) . self::ENCLOSURE . self::DELIMITER;
            }
        }

        return $contents;
    }

    /**
     * 写文件
     *
     * @param   resource  $resource
     * @param   array  $data
     * @return  void
     */
    private static function writeFile($resource, array $data)
    {
        // 自定义一个数组header, 与cell分开处理
        if (!empty($data['header'])) {
            fwrite($resource, self::ENCLOSURE
                . join(self::ENCLOSURE . self::DELIMITER . self::ENCLOSURE, $data['header'])
                . self::ENCLOSURE
                . self::EOL);
        }

        // cell中需要判断是否为长数字
        foreach ($data['rows'] as $row) {
            if (is_array($row)) {
                fwrite($resource, rtrim(self::formatRows($row), self::DELIMITER) . self::EOL);
            } else {
                fwrite($resource, $row . self::EOL);
            }
        }
    }
}
