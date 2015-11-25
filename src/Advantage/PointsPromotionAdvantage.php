<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Properties\City;
use CultuurNet\UiTPASBeheer\Properties\CityCollection;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
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

        $advantage = new static(
            $id,
            $title,
            $points,
            $exchangeable
        );

        if (!empty($promotion->description1)) {
            $advantage = $advantage->withDescription1(new StringLiteral($promotion->description1));
        }

        if (!empty($promotion->description2)) {
            $advantage = $advantage->withDescription2(new StringLiteral($promotion->description2));
        }

        if (!empty($promotion->validForCities)) {
            $cityCollection = new CityCollection();

            foreach ($promotion->validForCities as $city) {
                $cityCollection = $cityCollection->with(new City($city));
            }

            $advantage = $advantage->withValidForCities($cityCollection);
        }

        if (!empty($promotion->counters)) {
            $advantage = $advantage->withValidForCounters($promotion->counters);
        }

        if (!empty($promotion->cashingPeriodEnd)) {
            $dateParts = explode('-', $promotion->cashingPeriodEnd);
            $dateParts[1] = $dateParts[1] - 1;
            $advantage = $advantage->withEndDate(new Date(
                new Year($dateParts[0]),
                Month::getByOrdinal($dateParts[1]),
                new MonthDay((int)$dateParts[2])
            ));
        }

        return $advantage;
    }
}
