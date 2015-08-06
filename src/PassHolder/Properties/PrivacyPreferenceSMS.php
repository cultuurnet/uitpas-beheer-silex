<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\Enum\Enum;

/**
 * @method static ALL
 * @method static NOTIFICATION
 * @method static NO
 */
final class PrivacyPreferenceSMS extends Enum
{
    const ALL = 'ALL_SMS';
    const NOTIFICATION = 'NOTIFICATION_SMS';
    const NO = 'NO_SMS';
}
