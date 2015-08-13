<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\Enum\Enum;

/**
 * @method static KANSENTARIEF
 * @method static COUPON
 */
class TariffType extends Enum
{
    const KANSENTARIEF = 'KANSENTARIEF';
    const COUPON = 'COUPON';
}
