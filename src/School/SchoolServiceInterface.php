<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use ValueObjects\StringLiteral\StringLiteral;

interface SchoolServiceInterface
{
    public function get(StringLiteral $uuid);

    public function getSchools();
}
