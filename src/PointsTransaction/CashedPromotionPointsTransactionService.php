<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;

class CashedPromotionPointsTransactionService extends CounterAwareUitpasService implements
    PointsTransactionServiceInterface
{
    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     */
    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Date $startDate
     * @param Date $endDate
     * @return PointsTransaction|null
     */
    public function search(UiTPASNumber $uitpasNumber, Date $startDate, Date $endDate)
    {
        $query = new \CultureFeed_Uitpas_Passholder_Query_SearchCashedInPromotionPointsOptions();
        $query->balieConsumerKey = $this->getCounterConsumerKey();
        $query->uitpasNumber = $uitpasNumber->toNative();
        $query->cashingPeriodBegin = $startDate->toNativeDateTime()->getTimestamp();
        $query->cashingPeriodEnd = $endDate->toNativeDateTime()->getTimestamp();
        $query->max = 100;

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
