<?php

namespace Zeigo\Illuminate\Helpers;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Format data as CSV.
 */
class CsvHelper
{
    const EOL = "\r\n";

    const DELIMITER = ',';

    /**
     * Save data to CSV file.
     *
     * @param string $filePathName
     * @param array $data
     * @param bool $overwrite
     * @return string
     */
    public static function save(string $filePathName, array $data, bool $overwrite = false): string
    {
        if (! Str::endsWith($filePathName, '.csv')) {
            throw new InvalidArgumentException('The file extension must be .csv');
        }

        if (empty($data['rows'])) {
            throw new InvalidArgumentException('Missing required data for rows.');
        }

        if (is_file($filePathName) && ! $overwrite) {
            throw new InvalidArgumentException('File already exists.');
        }

        $resource = fopen($filePathName, 'w');

        // utf8 bom
        fwrite($resource, pack('H*', 'EFBBBF'));

        self::writeFile($resource, $data);

        fclose($resource);

        return self::parseToRealPath($filePathName);
    }

    /**
     * Data writes are always appended.
     *
     * @param string $filePathName
     * @param array $data
     * @return string
     */
    public static function append(string $filePathName, array $data): string
    {
        if (empty($data['rows'])) {
            throw new InvalidArgumentException('Missing required data for rows.');
        }

        if (! is_file($filePathName)) {
            throw new InvalidArgumentException('File not exists.');
        }

        $resource = fopen($filePathName, 'a');

        self::writeFile($resource, $data);

        fclose($resource);

        return self::parseToRealPath($filePathName);
    }

    /**
     * Send file content to output buffer.
     *
     * @param string $filename
     * @param array $data
     * @return void
     */
    public static function download(string $filename, array $data)
    {
        if (empty($data['rows'])) {
            throw new InvalidArgumentException('Missing required data for rows.');
        }

        // Append file suffix.
        $filename = trim($filename);
        if (! Str::endsWith($filename, '.csv')) {
            $filename .= '.csv';
        }

        ob_end_clean();
        ob_start();

        header('Content-Type:text/csv;charset=utf8');
        header('Content-Disposition:attachment;filename=' . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:Mon, 26 Jul 1997 05:00:00 GMT');
        header('Pragma:public');

        // utf8 bom
        echo pack('H*', 'EFBBBF');

        if (! empty($data['header'])) {
            echo '"' . join('"' . self::DELIMITER . '"', $data['header']) . '"' . self::EOL;
        }

        foreach ($data['rows'] as $row) {
            if (is_array($row)) {
                echo rtrim(self::formatRows($row), self::DELIMITER) . self::EOL;
            } else {
                echo $row . self::EOL;
            }
        }

        ob_end_flush();
        exit(0);
    }

    private static function parseToRealPath(string $path): string
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
                if (Str::startsWith($idx, 'CELL_FMT_')) {
                    /**
                     * CSV empty formula
                     * ="00001"
                     */
                    $contents .= '="' . $cell . '"' . self::DELIMITER;
                } else {
                    $contents .= '"' . $cell . '"' . self::DELIMITER;
                }
            } else {
                $contents .= '"' . str_replace('"', '""', $cell) . '"' . self::DELIMITER;
            }
        }

        return $contents;
    }

    /**
     * Write file.
     *
     * @param resource $resource
     * @param array $data
     * @return void
     */
    private static function writeFile($resource, array $data)
    {
        if (! empty($data['header'])) {
            fwrite(
                $resource,
                '"' . join('"' . self::DELIMITER . '"', $data['header']) . '"' . self::EOL
            );
        }

        foreach ($data['rows'] as $row) {
            if (is_array($row)) {
                fwrite($resource, rtrim(self::formatRows($row), self::DELIMITER) . self::EOL);
            } else {
                fwrite($resource, $row . self::EOL);
            }
        }
    }
}
