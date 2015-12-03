<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use ValueObjects\Enum\Enum;

/**
 * @method static CASHED_PROMOTION
 * @method static CHECKIN_ACTIVITY
 */
class PointsTransactionType extends Enum
{
    const CASHED_PROMOTION = 'cashed-promotion';
    const CHECKIN_ACTIVITY = 'checkin-activity';
}
