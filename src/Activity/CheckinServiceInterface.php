<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

interface CheckinServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @param string $eventCdbid
     *
     * @return int
     */
    public function checkin(UiTPASNumber $uitpasNumber, $eventCdbid);
}
