<?php

namespace Zeigo\Illuminate\Helpers;

use InvalidArgumentException;

/**
 * 数值辅助函数
 */
class NumberHelper
{
    /**
     * 返回字符值的round结果
     *
     * @param   float|null  $num
     * @param   int  $precision
     * @return  string
     */
    public static function round(float $num = null, int $precision = 0): string
    {
        return strval(round($num, $precision));
    }

    /**
     * 检查float小数位数是否符合要求
     *
     * @param   float  $number
     * @param   int  $decimals
     * @return  bool
     */
    public static function checkDecimal(float $number, int $decimals): bool
    {
        return 1 === preg_match('/^[0-9]+(.[0-9]{1,' . $decimals . '})?$/', abs($number));
    }

    /**
     * 精确的向上取整
     *
     *   解决下列情况下的取整精度
     *   >>> $num = 0
     *   => 0
     *   >>> $num += (837.000/8.37)
     *   => 100.0
     *   >>> ceil($num)
     *   => 101.0
     *   >>> Sunong\Component\Helpers\NumberHelper::strictCeil($num)
     *   => 100
     *
     * @param   numeric  $value
     * @return  int
     */
    public static function ceil($value): int
    {
        return intval(ceil(strval($value)));
    }

    /**
     * 舍位法取指定小数位数
     *
     * @param   float  $number
     * @param   int  $decimals
     * @return  string
     */
    public static function floorDecimal(float $number, int $decimals): string
    {
        if (0 > $decimals || 13 < $decimals) {
            throw new InvalidArgumentException('你是不是有毒');
        }

        if (0 < $number) {
            return strval(floor(strval($number * $digit = pow(10, $decimals))) / $digit);
        } else {
            return strval(ceil(strval($number * $digit = pow(10, $decimals))) / $digit);
        }
    }

    /**
     * 计算百分比
     *
     * @param   float|null  $dividend
     * @param   float  $divisor
     * @param   int  $decimals
     * @param   bool  $percentSign  可选附加百分号用于输出
     * @return  string
     */
    public static function percentage(float $dividend = null, float $divisor, int $decimals = 2, bool $percentSign = true): string
    {
        return sprintf(
            "%.{$decimals}f",
            $divisor ? round($dividend / $divisor * 100, $decimals) : '0'
        ) . ($percentSign ? '%' : '');
    }
}
