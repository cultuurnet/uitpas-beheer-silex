<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas_Passholder_Query_CheckInPassholderOptions;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;

class CheckinService extends CounterAwareUitpasService implements CheckinServiceInterface
{
    /**
     * @inheritdoc
     */
    public function checkin(UiTPASNumber $uitpasNumber, Cdbid $eventCdbid)
    {
        $checkinOptions = new CultureFeed_Uitpas_Passholder_Query_CheckInPassholderOptions();
        $checkinOptions->uitpasNumber = $uitpasNumber->getNumber();
        $checkinOptions->cdbid = $eventCdbid->toNative();
        $checkinOptions->balieConsumerKey = $this->getCounterConsumerKey();

        $points = $this->getUitpasService()->checkinPassholder($checkinOptions);
        return $points;
    }
}
