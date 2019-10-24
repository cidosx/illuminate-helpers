<?php

namespace Zeigo\Illuminate\Helpers;

use InvalidArgumentException;

class NumberHelper
{
    /**
     * Rounds a float and return string cast result.
     *
     * @param float|null $value
     * @param int $precision
     * @param int $mode
     * @return string
     */
    public static function round(float $value = null, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): string
    {
        return strval(round($value, $precision, $mode));
    }

    /**
     * Determine given float decimal places.
     *
     * @param float $value
     * @param int $decimals
     * @return bool
     */
    public static function checkDecimal(float $value, int $decimals): bool
    {
        return 1 === preg_match('/^[0-9]+(.[0-9]{1,' . $decimals . '})?$/', abs($value));
    }

    /**
     * Round fractions up exactly.
     *
     * @param numeric $value
     * @return int
     */
    public static function ceil($value): int
    {
        ///////////////////////////////
        // Resolve such a case like: //
        ///////////////////////////////
        // >>> $num = 0
        // => 0
        // >>> $num += (837.000/8.37)
        // => 100.0
        // >>> ceil($num)
        // => 101.0
        // >>> Zeigo\Illuminate\Helpers\NumberHelper::ceil($num)
        // => 100
        ///////////////////////////////

        return intval(ceil(strval($value)));
    }

    /**
     * Round fractions down for float and return string cast result.
     *
     * @param float $value
     * @param int $decimals
     * @return string
     */
    public static function floorDecimal(float $value, int $decimals): string
    {
        if (0 > $decimals || 13 < $decimals) {
            throw new InvalidArgumentException('Hmmmmmmm.');
        }

        if (0 < $value) {
            return strval(floor(strval($value * $digit = pow(10, $decimals))) / $digit);
        } else {
            return strval(ceil(strval($value * $digit = pow(10, $decimals))) / $digit);
        }
    }

    /**
     * A percentage printer.
     *
     * @param float|null $dividend
     * @param float $divisor
     * @param int $decimals
     * @param bool $percentSign
     * @return string
     */
    public static function percentage(float $dividend = null, float $divisor, int $decimals = 2, bool $percentSign = true): string
    {
        return sprintf(
            "%.{$decimals}f",
            $divisor ? round($dividend / $divisor * 100, $decimals) : '0'
        ) . ($percentSign ? '%' : '');
    }
}
