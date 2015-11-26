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
    public function it_can_create_an_instance_from_a_culturefeed_points_promotion_object_with_correct_cashin_state(
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

    /**
     * @test
     */
    public function it_can_create_an_instance_from_a_culturefeed_points_promotion_object()
    {
        $id = new StringLiteral('10');
        $title = new StringLiteral('Delicious coffee');
        $points = new Integer(5);
        $exchangeable = true;

        $description1 = 'First description';
        $description2 = 'Second description';
        $validForCities = [
            "Brussel",
            "Leuven",
        ];
        $validForCounters = array();
        for ($i = 0; $i <= 2; $i++) {
            $counter = new \CultureFeed_Uitpas_Passholder_Counter();
            $counter->id = $i + 1;
            $counter->name = "counter " . $counter->id;

            $validForCounters[$i] = $counter;
        }

        $endDate = '1483225199';

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

        $cfPromotion->description1 = $description1;
        $cfPromotion->description2 = $description2;
        $cfPromotion->validForCities = $validForCities;
        $cfPromotion->counters = $validForCounters;
        $cfPromotion->cashingPeriodEnd = $endDate;

        $advantage = PointsPromotionAdvantage::fromCultureFeedPointsPromotion($cfPromotion);

        $validForCitiesCollection = (new CityCollection())
            ->with(new City("Brussel"))
            ->with(new City("Leuven"));

        $endDatValueObject = new Date(
            new Year(2016),
            Month::DECEMBER(),
            new MonthDay(31)
        );

        $expectedAdvantage = (new PointsPromotionAdvantage(
            $id,
            $title,
            $points,
            $exchangeable
        ))
        ->withDescription1(new StringLiteral($description1))
        ->withDescription2(new StringLiteral($description2))
        ->withValidForCities($validForCitiesCollection)
        ->withValidForCounters($validForCounters)
        ->withEndDate($endDatValueObject);

        $this->assertEquals(
            $expectedAdvantage,
            $advantage
        );
    }
}
