<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PointsPromotionAdvantage extends Advantage
{
    /**
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param \ValueObjects\Number\Integer $points
     * @param bool $exchangeable
     */
    public function __construct(StringLiteral $id, StringLiteral $title, Integer $points, $exchangeable)
    {
        parent::__construct(
            AdvantageType::POINTS_PROMOTION(),
            $id,
            $title,
            $points,
            $exchangeable
        );
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_PointsPromotion $promotion
     * @return static
     */
    public static function fromCultureFeedPointsPromotion(\CultureFeed_Uitpas_Passholder_PointsPromotion $promotion)
    {
        $id = new StringLiteral((string) $promotion->id);
        $title = new StringLiteral((string) $promotion->title);
        $points = new Integer($promotion->points);
        $exchangeable = ($promotion->cashInState == $promotion::CASHIN_POSSIBLE);

        $description1 = !is_null($promotion->description1) ? $promotion->description1 : null;
        $description2 = !is_null($promotion->description2) ? $promotion->description2 : null;
        $validForCities = !is_null($promotion->validForCities) ? $promotion->validForCities : null;
        //$validForCounters = !isnull($promotion->valid)
        //$endDate = !isnull($promotion->end)

        return new static(
            $id,
            $title,
            $points,
            $exchangeable
        );
    }
}
