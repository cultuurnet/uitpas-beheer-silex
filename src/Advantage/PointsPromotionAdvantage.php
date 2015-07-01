<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

class PointsPromotionAdvantage extends Advantage
{
    /**
     * @param \CultureFeed_PointsPromotion $promotion
     * @return static
     */
    public static function fromCultureFeedPointsPromotion(\CultureFeed_PointsPromotion $promotion)
    {
        return new static(
            $promotion->id,
            $promotion->title,
            $promotion->points
        );
    }
}
