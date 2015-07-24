<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas_Passholder_Query_CheckInPassholderOptions;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class CheckinService extends CounterAwareUitpasService implements CheckinServiceInterface
{
    /**
     * @inheritdoc
     */
    public function checkin(UiTPASNumber $uitpasNumber, $eventCdbid)
    {
        $checkinOptions = new CultureFeed_Uitpas_Passholder_Query_CheckInPassholderOptions();
        $checkinOptions->uitpasNumber = $uitpasNumber->getNumber();
        $checkinOptions->cdbid = $eventCdbid;
        $checkinOptions->balieConsumerKey = $this->getCounterConsumerKey();

        $points = $this->getUitpasService()->checkinPassholder($checkinOptions);
        return $points;
    }
}
