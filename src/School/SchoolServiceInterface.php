<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use ValueObjects\StringLiteral\StringLiteral;

interface SchoolServiceInterface
{
    /**
     * @param StringLiteral $uuid
     * @return School|null
     */
    public function get(StringLiteral $uuid);

    /**
     * @return SchoolCollection
     */
    public function getSchools();
}
