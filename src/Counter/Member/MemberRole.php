<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use ValueObjects\Enum\Enum;

/**
 * @method static MemberRole ADMIN()
 * @method static MemberRole MEMBER()
 */
class MemberRole extends Enum
{
    const ADMIN = 'ADMIN';
    const MEMBER = 'MEMBER';
}
