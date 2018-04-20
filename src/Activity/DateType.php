<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\Enum\Enum;

/**
 * @method static TODAY
 * @method static NEXT_7_DAYS
 * @method static NEXT_30_DAYS
 * @method static NEXT_12_MONTHS
 * @method static PAST
 */
class DateType extends Enum
{
    const TODAY = 'today';
    const NEXT_7_DAYS = 'next_7_days';
    const NEXT_30_DAYS = 'next_30_days';
    const NEXT_12_MONTHS = 'next_12_months';
    const PAST = 'past';
    const CHOOSE_DATE = 'choose_date';
}
