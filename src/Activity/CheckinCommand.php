<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Activity\Cdbid;

class CheckinCommand
{
    /**
     * @var Cdbid
     */
    protected $eventCdbid;

    public function __construct(Cdbid $eventCdbid)
    {
        $this->eventCdbid = $eventCdbid;
    }

    /**
     * @return Cdbid
     */
    public function getEventCdbid()
    {
        return $this->eventCdbid;
    }
}
