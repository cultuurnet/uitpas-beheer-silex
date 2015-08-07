<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;

interface CheckinServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Cdbid $eventCdbid
     *
     * @return int
     */
    public function checkin(UiTPASNumber $uitpasNumber, Cdbid $eventCdbid);
}
