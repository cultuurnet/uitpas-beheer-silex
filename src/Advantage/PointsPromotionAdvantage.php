<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

class PointsPromotionAdvantage extends Advantage
{
    /**
     * @param string $id
     * @param string $title
     * @param int $points
     */
    public function __construct($id, $title, $points)
    {
        parent::__construct(
            AdvantageType::POINTS_PROMOTION(),
            $id,
            $title,
            $points
        );
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_PointsPromotion $promotion
     * @return static
     */
    public static function fromCultureFeedPointsPromotion(\CultureFeed_Uitpas_Passholder_PointsPromotion $promotion)
    {
        return new static(
            $promotion->id,
            $promotion->title,
            $promotion->points
        );
    }
}
