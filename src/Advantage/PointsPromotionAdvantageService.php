<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class PointsPromotionAdvantageService extends CounterAwareUitpasService implements AdvantageServiceInterface
{
    /**
     * @return AdvantageType
     */
    public function getType()
    {
        return AdvantageType::POINTS_PROMOTION();
    }

    /**
     * @return PointsPromotionAdvantage|null
     */
    public function get(UiTPASNumber $uitpasNumber, StringLiteral $id)
    {
        $id = $id->toNative();

        $passholderParameters = new \CultureFeed_Uitpas_Promotion_PassholderParameter();
        $passholderParameters->uitpasNumber = $uitpasNumber->toNative();

        try {
            $advantage = $this->getUitpasService()->getPointsPromotion($id, $passholderParameters);
            return PointsPromotionAdvantage::fromCultureFeedPointsPromotion($advantage);
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return PointsPromotionAdvantage[]
     */
    public function getCashable(UiTPASNumber $uitpasNumber)
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

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     * @return Advantage
     */
    public function cashIn(UiTPASNumber $uitpasNumber, StringLiteral $id)
    {
        // TODO: Implement cash() method.
    }
}
