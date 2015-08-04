<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\Enum\Enum;

/**
 * @method static ALL
 * @method static NOTIFICATION
 * @method static NO
 */
final class PrivacyPreferenceEmail extends Enum
{
    const ALL = 'ALL_MAILS';
    const NOTIFICATION = 'NOTIFICATION_MAILS';
    const NO = 'NO_MAILS';
}
