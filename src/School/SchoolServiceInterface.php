<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

interface SchoolServiceInterface
{
    public function getSchool($uuid);

    public function getSchools();
}
