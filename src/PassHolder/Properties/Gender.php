<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\Enum\Enum;

/**
 * @method static Gender MALE
 * @method static Gender FEMALE
 */
final class Gender extends Enum
{
    const MALE = 'MALE';
    const FEMALE = 'FEMALE';
}
