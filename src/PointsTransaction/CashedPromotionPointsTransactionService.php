<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\Clock\Clock;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;

class CashedPromotionPointsTransactionService extends CounterAwareUitpasService implements
    PointsTransactionServiceInterface
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
        $query = new \CultureFeed_Uitpas_Passholder_Query_SearchCashedInPromotionPointsOptions();
        $query->balieConsumerKey = $this->getCounterConsumerKey();
        $query->uitpasNumber = $uitpasNumber->toNative();
        $currentTime = $this->clock->getDateTime()->getTimestamp();
        $query->cashingPeriodBegin = $currentTime;
        $query->cashingPeriodEnd = strtotime("-1 year", $currentTime);

        try {
            $result = $this->getUitpasService()->getCashedInPromotionPoints($query);

            $cashedPromotions = array_map(
                function (\CultureFeed_Uitpas_Passholder_CashedInPointsPromotion $cashedInPointsPromotion) {
                    return CashedPromotionPointsTransaction::fromCultureFeedCashedInPromotion(
                        $cashedInPointsPromotion
                    );
                },
                $result->objects
            );

            return $cashedPromotions;
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }
}
