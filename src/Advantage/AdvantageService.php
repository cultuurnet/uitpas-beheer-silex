<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class AdvantageService extends CounterAwareUitpasService
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @return WelcomeAdvantage[]
     */
    public function getExchangeableWelcomeAdvantages(UiTPASNumber $uitpasNumber)
    {
        $advantages = array();

        $options = new \CultureFeed_Uitpas_Passholder_Query_WelcomeAdvantagesOptions();

        $options->balieConsumerKey = $this->getCounterConsumerKey();
        $options->uitpas_number = $uitpasNumber->toNative();

        $options->cashingPeriodBegin = time();
        $options->cashingPeriodEnd = time();

        $options->cashedIn = false;

        $results = $this->getUitpasService()->getWelcomeAdvantagesForPassholder($options);

        foreach ($results->objects as $advantage) {
            $advantages[] = WelcomeAdvantage::fromCultureFeedWelcomeAdvantage($advantage);
        }

        return $advantages;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return PointsPromotionAdvantage[]
     */
    public function getExchangeablePointPromotions(UiTPASNumber $uitpasNumber)
    {
        $advantages = array();

        $options = new \CultureFeed_Uitpas_Passholder_Query_SearchPromotionPointsOptions();

        $options->balieConsumerKey = $this->getCounterConsumerKey();
        $options->uitpasNumber = $uitpasNumber->toNative();

        $options->cashingPeriodBegin = time();
        $options->cashingPeriodEnd = time();

        $options->filterOnUserPoints = true;
        $options->unexpired = true;

        $results = $this->getUitpasService()->getPromotionPoints($options);

        foreach ($results->objects as $advantage) {
            $advantages[] = PointsPromotionAdvantage::fromCultureFeedPointsPromotion($advantage);
        }

        return $advantages;
    }
}
