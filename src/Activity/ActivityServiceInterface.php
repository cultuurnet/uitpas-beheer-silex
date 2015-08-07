<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

interface ActivityServiceInterface
{
    /**
     * @param mixed $query
     *
     * @return PagedResultSet
     */
    public function search($query);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Cdbid $eventCdbid
     * @return Activity
     */
    public function get(UiTPASNumber $uitpasNumber, Cdbid $eventCdbid);
}
