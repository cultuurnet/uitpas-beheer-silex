<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\Clock\Clock;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;

class CheckinPointsTransactionService extends CounterAwareUitpasService implements PointsTransactionServiceInterface
{
    /**
     * @var Clock
     */
    protected $clock;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param Clock $clock
     */
    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        Clock $clock
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);

        $this->clock = $clock;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Date $startDate
     * @param Date $endDate
     * @return PointsTransaction|null
     */
    public function get(UiTPASNumber $uitpasNumber, Date $startDate, Date $endDate)
    {
        $uitpasService = $this->getUitpasService();

        // Fetch the pass holder to get its internal ID which we need
        // to make the search request.
        $passHolder = $uitpasService->getPassholderByUitpasNumber(
            $uitpasNumber->toNative(),
            $this->getCounterConsumerKey()
        );

        $query = new \CultureFeed_Uitpas_Event_Query_SearchCheckinsOptions();
        $query->balieConsumerKey = $this->getCounterConsumerKey();
        $query->uid = $passHolder->uitIdUser->id;
        $currentTime = $this->clock->getDateTime()->getTimestamp();
        $query->startDate = $currentTime;
        $query->endDate = strtotime("-1 year", $currentTime);

        try {
            $result = $this->getUitpasService()->searchCheckins($query);

            $checkins = array_map(
                function (\CultureFeed_Uitpas_Event_CheckinActivity $checkin) {
                    return CheckinPointsTransaction::fromCultureFeedEventCheckin($checkin);
                },
                $result->objects
            );

            return $checkins;
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }
}
