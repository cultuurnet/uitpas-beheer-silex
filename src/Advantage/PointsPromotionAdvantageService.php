<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Clock\Clock;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class PointsPromotionAdvantageService extends CounterAwareUitpasService implements AdvantageServiceInterface
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
     * @param StringLiteral $id
     * @return PointsPromotionAdvantage|null
     */
    public function get(UiTPASNumber $uitpasNumber, StringLiteral $id)
    {
        $id = $id->toNative();

        $passHolderParameters = new \CultureFeed_Uitpas_Promotion_PassholderParameter();
        $passHolderParameters->uitpasNumber = $uitpasNumber->toNative();

        try {
            $advantage = $this->getUitpasService()->getPointsPromotion($id, $passHolderParameters);
            return PointsPromotionAdvantage::fromCultureFeedPointsPromotion($advantage);
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param int $max
     * @param int $start
     * @return PointsPromotionAdvantage[]
     */
    public function getExchangeable(UiTPASNumber $uitpasNumber, $max = null, $start = null)
    {
        $advantages = array();

        $options = new \CultureFeed_Uitpas_Passholder_Query_SearchPromotionPointsOptions();

        $options->balieConsumerKey = $this->getCounterConsumerKey();
        $options->uitpasNumber = $uitpasNumber->toNative();

        $currentTimestamp = $this->clock->getDateTime()->getTimestamp();
        $options->cashingPeriodBegin = $options->cashingPeriodEnd = $currentTimestamp;

        $options->filterOnUserPoints = true;
        $options->unexpired = true;

        if (!empty($max)) {
            $options->max = $max;
        }

        if (!empty($start)) {
            $options->start = $start;
        }

        $results = $this->getUitpasService()->getPromotionPoints($options);

        /* @var \CultureFeed_Uitpas_Passholder_PointsPromotion $advantage */
        foreach ($results->objects as $advantage) {
            if ($advantage->cashInState == $advantage::CASHIN_POSSIBLE) {
                $advantages[] = PointsPromotionAdvantage::fromCultureFeedPointsPromotion($advantage);
            }
        }

        return $advantages;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     */
    public function exchange(UiTPASNumber $uitpasNumber, StringLiteral $id)
    {
        $this->getUitpasService()->cashInPromotionPoints(
            $uitpasNumber->toNative(),
            $id->toNative(),
            $this->getCounterConsumerKey()
        );
    }
}
