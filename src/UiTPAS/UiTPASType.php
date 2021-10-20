<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use ValueObjects\Enum\Enum;

/**
 * @method static CARD
 * @method static KEY
 * @method static STICKER
 * @method static DIGITAL
 */
class UiTPASType extends Enum
{
    const CARD = 'CARD';
    const KEY = 'KEY';
    const STICKER = 'STICKER';
    const DIGITAL = 'DIGITAL';
}
