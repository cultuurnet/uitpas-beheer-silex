<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use ValueObjects\Enum\Enum;

/**
 * @method static MembershipStatus ACTIVE()
 * @method static MembershipStatus EXPIRED()
 */
class MembershipStatus extends Enum
{
    const ACTIVE = 'ACTIVE';
    const EXPIRED = 'EXPIRED';
}
