<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\ChecksumValidation;

class ChecksumValidator
{
    /**
     * @param int $number
     * @return bool
     * @author WN
     */
    public static function validateNumber($number)
    {
        return (bool)!self::checksum($number, true);
    }

    /**
     * @param int|string $number
     * @param bool $check Set to true if you are calculating checksum for validation
     * @return int
     * @author WN
     */
    private static function checksum($number, $check = false)
    {
        $data = str_split(strrev($number));

        $sum = 0;

        foreach ($data as $k => $v) {

            $tmp = $v + $v * (int)(($k % 2) xor !$check);

            if ($tmp > 9) {
                $tmp -= 9;
            }

            $sum += $tmp;
        }

        $sum %= 10;

        return (int)$sum == 0 ? 0 : 10 - $sum;
    }
}
