<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use ValueObjects\Enum\Enum;

/**
 * @method static DAY
 * @method static WEEK
 * @method static MONTH
 * @method static QUARTER
 * @method static YEAR
 * @method static ABSOLUTE
 */
class PeriodType extends Enum
{
    const DAY = 'DAY';
    const WEEK = 'WEEK';
    const MONTH = 'MONTH';
    const QUARTER = 'QUARTER';
    const YEAR = 'YEAR';
    const ABSOLUTE = 'ABSOLUTE';
}
