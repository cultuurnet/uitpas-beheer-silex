<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 01/12/15
 * Time: 16:47
 */

namespace CultuurNet\UiTPASBeheer\PointsTransaction;


use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class CashedPromotionPointsTransactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider pointsTransactionDataProvider
     *
     * @param Date $creationDate
     * @param StringLiteral $title
     * @param \ValueObjects\Number\Integer $points
     */
    public function it_initiates_objects_with_a_fixed_type_and_stores_all_other_info(
        Date $creationDate,
        StringLiteral $title,
        Integer $points
    ) {
        $cashedPromotion = new CashedPromotionPointsTransaction($creationDate, $title, $points);

        $this->assertTrue($cashedPromotion->getType()->sameValueAs(PointsTransactionType::CASHED_PROMOTION()));

        $this->assertEquals($creationDate, $cashedPromotion->getCreationDate());
        $this->assertEquals($title, $cashedPromotion->getTitle());
        $this->assertEquals($points, $cashedPromotion->getPoints());
    }

    /**
     * @test
     * @dataProvider pointsTransactionDataProvider
     *
     * @param Date $creationDate
     * @param StringLiteral $title
     * @param \ValueObjects\Number\Integer $points
     */
    public function it_can_create_an_instance_from_a_culturefeed_points_promotion_object(
        Date $creationDate,
        StringLiteral $title,
        Integer $points
    ) {
        $cfCashedPromotion = new \CultureFeed_Uitpas_Passholder_CashedInPointsPromotion();
        $cfCashedPromotion->cashingDate = $creationDate->toNativeDateTime()->format('U');
        $cfCashedPromotion->title = $title->toNative();
        $cfCashedPromotion->points = $points->toNative();

        $cashedPromotion = CashedPromotionPointsTransaction::fromCultureFeedCashedInPromotion($cfCashedPromotion);

        $expectedCashedPromotion = new CashedPromotionPointsTransaction(
            $creationDate,
            $title,
            $points
        );

        $this->assertEquals($expectedCashedPromotion, $cashedPromotion);
    }

    /**
     * @return array
     */
    public function pointsTransactionDataProvider()
    {
        return [
            [
                new Date(
                    new Year(2015),
                    Month::DECEMBER(),
                    new MonthDay(4)
                ),
                new StringLiteral('Delicious coffee'),
                new Integer(5)
            ],
            [
                new Date(
                    new Year(2015),
                    Month::JULY(),
                    new MonthDay(11)
                ),
                new StringLiteral('Event in the wild'),
                new Integer(10)
            ],
        ];
    }
}
