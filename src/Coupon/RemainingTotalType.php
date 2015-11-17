<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use ValueObjects\Enum\Enum;

/**
 * @method static DAY
 * @method static WEEK
 * @method static MONTH
 * @method static QUARTER
 * @method static YEAR
 * @method static ABSOLUTE
 */
class RemainingTotalType extends Enum
{
    const DAY = 'DAY';
    const WEEK = 'WEEK';
    const MONTH = 'MONTH';
    const QUARTER = 'QUARTER';
    const YEAR = 'YEAR';
    const ABSOLUTE = 'ABSOLUTE';
}
