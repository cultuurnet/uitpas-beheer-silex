<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Price;

use ValueObjects\Enum\Enum;

/**
 * @method static FIRST_CARD
 * @method static LOSS_THEFT
 * @method static REMOVAL
 * @method static LOSS_KANSENSTATUUT
 * @method static OBTAIN_KANSENSTATUUT
 * @method static CARD_UPGRADE
 * @method static EXTRA_CARD
 * @method static DEFECT
 */
class PurchaseReason extends Enum
{
    const FIRST_CARD = 'FIRST_CARD';
    const LOSS_THEFT = 'LOSS_THEFT';
    const REMOVAL = 'REMOVAL';
    const LOSS_KANSENSTATUUT = 'LOSS_KANSENSTATUUT';
    const OBTAIN_KANSENSTATUUT = 'OBTAIN_KANSENSTATUUT';
    const CARD_UPGRADE = 'CARD_UPGRADE';
    const EXTRA_CARD = 'EXTRA_CARD';
    const DEFECT = 'DEFECT';
}
