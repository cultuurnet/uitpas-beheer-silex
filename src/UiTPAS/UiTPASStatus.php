<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use ValueObjects\Enum\Enum;

/**
 * @method static LOCAL_STOCK
 * @method static STOCK
 * @method static SENT_TO_BALIE
 * @method static PROVISIONED
 * @method static ACTIVE
 * @method static BLOCKED
 * @method static DELETED
 */
class UiTPASStatus extends Enum
{
    const LOCAL_STOCK = 'LOCAL_STOCK';
    const STOCK = 'STOCK';
    const SENT_TO_BALIE = 'SENT_TO_BALIE';
    const PROVISIONED = 'PROVISIONED';
    const ACTIVE = 'ACTIVE';
    const BLOCKED = 'BLOCKED';
    const DELETED = 'DELETED';
}
