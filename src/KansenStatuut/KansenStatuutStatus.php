<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use ValueObjects\Enum\Enum;

/**
 * @method static ACTIVE
 * @method static IN_GRACE_PERIOD
 * @method static EXPIRED
 */
class KansenStatuutStatus extends Enum
{
    const ACTIVE = 'ACTIVE';
    const IN_GRACE_PERIOD = 'IN_GRACE_PERIOD';
    const EXPIRED = 'EXPIRED';
}
