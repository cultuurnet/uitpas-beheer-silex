<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use ValueObjects\Enum\Enum;

/**
 * @method static WELCOME
 * @method static POINTS_PROMOTION
 */
class AdvantageType extends Enum
{
    const WELCOME = 'welcome';
    const POINTS_PROMOTION = 'points-promotion';
}
