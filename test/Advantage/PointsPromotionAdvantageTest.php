<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PointsPromotionAdvantageTest extends \PHPUnit_Framework_TestCase
{
    use AdvantageAssertionTrait;

    /**
     * @test
     * @dataProvider pointPromotionDataProvider
     *
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param \ValueObjects\Number\Integer $points
     * @param $exchangeable
     */
    public function it_initiates_objects_with_a_fixed_type_and_stores_all_other_info(
        StringLiteral $id,
        StringLiteral $title,
        Integer $points,
        $exchangeable
    ) {
        $advantage = new PointsPromotionAdvantage($id, $title, $points, $exchangeable);

        $this->assertTrue($advantage->getType()->sameValueAs(AdvantageType::POINTS_PROMOTION()));

        $this->assertAdvantageData(
            $advantage,
            $id,
            $title,
            $points,
            $exchangeable
        );
    }

    /**
     * @test
     * @dataProvider pointPromotionDataProvider
     *
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param \ValueObjects\Number\Integer $points
     * @param $exchangeable
     */
    public function it_can_create_an_instance_from_a_culturefeed_points_promotion_object(
        StringLiteral $id,
        StringLiteral $title,
        Integer $points,
        $exchangeable
    ) {
        $cfPromotion = new \CultureFeed_Uitpas_Passholder_PointsPromotion(
            $id,
            $title,
            $points
        );

        if ($exchangeable) {
            $cfPromotion->cashInState = \CultureFeed_Uitpas_Passholder_PointsPromotion::CASHIN_POSSIBLE;
        } else {
            $cfPromotion->cashInState =
                \CultureFeed_Uitpas_Passholder_PointsPromotion::CASHIN_NOT_POSSIBLE_POINTS_CONSTRAINT;
        }

        $advantage = PointsPromotionAdvantage::fromCultureFeedPointsPromotion($cfPromotion);

        $this->assertAdvantageData(
            $advantage,
            $id,
            $title,
            $points,
            $exchangeable
        );
    }

    /**
     * @return array
     */
    public function pointPromotionDataProvider()
    {
        return [
            [
                new StringLiteral('10'),
                new StringLiteral('Delicious coffee'),
                new Integer(5),
                true,
            ],
            [
                new StringLiteral('11'),
                new StringLiteral('Expired offer'),
                new Integer(10),
                false,
            ],
        ];
    }
}
