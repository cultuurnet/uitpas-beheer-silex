<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 02/12/15
 * Time: 09:58
 */

namespace CultuurNet\UiTPASBeheer\PointsTransaction;


use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class CheckinPointsTransactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function setUp()
    {
        //date_default_timezone_set('Europe/Brussels');
    }

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
        $checkin = new CheckinPointsTransaction($creationDate, $title, $points);

        $this->assertTrue($checkin->getType()->sameValueAs(PointsTransactionType::CHECKIN_ACTIVITY()));

        $this->assertEquals($creationDate, $checkin->getCreationDate());
        $this->assertEquals($title, $checkin->getTitle());
        $this->assertEquals($points, $checkin->getPoints());
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
        $cfCheckinActivity = new \CultureFeed_Uitpas_Event_CheckinActivity();
        $cfCheckinActivity->creationDate = $creationDate->toNativeDateTime()->getTimestamp();
        $cfCheckinActivity->nodeTitle = $title->toNative();
        $cfCheckinActivity->points = $points->toNative();

        $checkin = CheckinPointsTransaction::fromCultureFeedEventCheckin($cfCheckinActivity);

        $expectedCheckin = new CheckinPointsTransaction(
            $creationDate,
            $title,
            $points
        );

        $this->assertEquals($expectedCheckin, $checkin);
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
