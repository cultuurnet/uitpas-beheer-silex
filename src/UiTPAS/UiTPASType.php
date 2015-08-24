<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use ValueObjects\Enum\Enum;

/**
 * @method static CARD
 * @method static KEY
 * @method static STICKER
 */
class UiTPASType extends Enum
{
    const CARD = 'CARD';
    const KEY = 'KEY';
    const STICKER = 'STICKER';
}
