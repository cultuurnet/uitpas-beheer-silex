<?php

namespace CultuurNet\UiTPASBeheer\Counter\Association;

use ValueObjects\Enum\Enum;

/**
 * @method static READ
 * @method static REGISTER
 * @method static ANY
 */
final class Permission extends Enum
{
    const READ = 'READ';
    const REGISTER = 'REGISTER';
    const ANY = 'ANY';
}
