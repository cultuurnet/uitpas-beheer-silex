<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultureFeed_Uitpas_Passholder;
use CultureFeed_Uitpas_Passholder_UitIdUser;
use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use DateTimeImmutable;
use DateTimeZone;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class CombinedPointsTransactionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var CultureFeed_Uitpas_Passholder
     */
    private $passHolder;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var CheckinPointsTransactionService
     */
    protected $checkinService;

    /**
     * @var CashedPromotionPointsTransactionService
     */
    protected $cashedPromotionService;

    /**
     * @var CombinedPointsTransactionService
     */
    protected $combinedPointsTransactionService;

    /**
     * @var int
     *
     * Current time, as a unix timestamp.
     */
    protected $startTime;

    /**
     * @var int
     *
     * Current time - 1 year, as a unix timestamp.
     */
    protected $endTime;

    public function setUp()
    {
        // Mock the system clock.
        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Brussels'));
        $clock = new FrozenClock($now);
        $this->startTime = $clock->getDateTime()->getTimestamp();
        $this->endTime = strtotime("-1 year", $this->startTime);

        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->checkinService = new CheckinPointsTransactionService(
            $this->uitpas,
            $this->counterConsumerKey,
            $clock
        );

        $this->cashedPromotionService = new CashedPromotionPointsTransactionService(
            $this->uitpas,
            $this->counterConsumerKey,
            $clock
        );

        $this->combinedPointsTransactionService = new CombinedPointsTransactionService(
            $this->checkinService,
            $this->cashedPromotionService
        );

        $passHolder = new CultureFeed_Uitpas_Passholder();
        $passHolder->uitIdUser = new CultureFeed_Uitpas_Passholder_UitIdUser();
        $passHolder->uitIdUser->id = '5';
        $passHolder->uitIdUser->name = 'John Doe';
        $this->passHolder = $passHolder;

        $this->uitpas->expects($this->any())
            ->method('getPassholderByUitpasNumber')
            ->with('0930000125607')
            ->willReturn(
                $this->passHolder
            );
    }

    /**
     * @test
     */
    public function it_can_get_the_points_history_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        // Prepare data for checkin service.
        $cfCheckinActivities = $this->get_checkin_service_data();

        // Prepare the data for the cashed promotions service.
        $cfCashedInPromotions = $this->get_cashed_promotions_service_data();

        // Get expected data.
        $expected = $this->get_expected_data();

        $startDate = Date::fromNativeDateTime(
            \DateTime::createFromFormat('U', $this->startTime)
        );

        $endDate = Date::fromNativeDateTime(
            \DateTime::createFromFormat('U', $this->endTime)
        );

        $this->uitpas->expects($this->once())
            ->method('searchCheckins')
            //->with($expectedOptions)
            ->willReturn($cfCheckinActivities);

        $this->uitpas->expects($this->once())
            ->method('getCashedInPromotionPoints')
            //->with($expectedOptions)
            ->willReturn($cfCashedInPromotions);

        $transactions = $this->combinedPointsTransactionService->search($uitpasNumber, $startDate, $endDate);

        $this->assertEquals($expected, $transactions);
    }

    /**
     * Helper function to prepare the data the checkin service will return.
     *
     * @return \CultureFeed_ResultSet
     */
    public function get_checkin_service_data()
    {
        $cfCheckinActivity1 = new \CultureFeed_Uitpas_Event_CheckinActivity();
        $cfCheckinActivity1->id = 1;
        $date = new Date(
            new Year(2015),
            Month::JANUARY(),
            new MonthDay(21)
        );
        $creationDate1 = $date->toNativeDateTime()->getTimestamp();
        $cfCheckinActivity1->creationDate = $creationDate1;
        $cfCheckinActivity1->nodeTitle = 'Event 21st of January';
        $cfCheckinActivity1->points = 10;

        $cfCheckinActivity2 = new \CultureFeed_Uitpas_Event_CheckinActivity();
        $cfCheckinActivity2->id = 1;
        $date = new Date(
            new Year(2015),
            Month::JULY(),
            new MonthDay(11)
        );
        $creationDate2 = $date->toNativeDateTime()->getTimestamp();
        $cfCheckinActivity2->creationDate = $creationDate2;
        $cfCheckinActivity2->nodeTitle = 'Event 11th of July';
        $cfCheckinActivity2->points = 5;

        $cfCheckinActivity3 = new \CultureFeed_Uitpas_Event_CheckinActivity();
        $cfCheckinActivity3->id = 2;
        $date = new Date(
            new Year(2015),
            Month::DECEMBER(),
            new MonthDay(4)
        );
        $creationDate3 = $date->toNativeDateTime()->getTimestamp();
        $cfCheckinActivity3->creationDate = $creationDate3;
        $cfCheckinActivity3->nodeTitle = 'Event 4th of December';
        $cfCheckinActivity3->points = 15;

        $cfCheckinActivityCollection = array();

        $cfCheckinActivityCollection[] = $cfCheckinActivity1;
        $cfCheckinActivityCollection[] = $cfCheckinActivity2;
        $cfCheckinActivityCollection[] = $cfCheckinActivity3;

        $cfCheckinActivities = new \CultureFeed_ResultSet(2, $cfCheckinActivityCollection);

        return $cfCheckinActivities;
    }

    /**
     * Helper function to prepare the data the cashed promotions service will return.
     *
     * @return \CultureFeed_ResultSet
     */
    public function get_cashed_promotions_service_data()
    {
        $cfCashedInPromoption1 = new \CultureFeed_Uitpas_Passholder_CashedInPointsPromotion();
        $cfCashedInPromoption1->id = 1;
        $date = new Date(
            new Year(2015),
            Month::MARCH(),
            new MonthDay(21)
        );
        $cashingDate1 = $date->toNativeDateTime()->getTimestamp();
        $cfCashedInPromoption1->cashingDate = $cashingDate1;
        $cfCashedInPromoption1->title = 'Star wars stickers';
        $cfCashedInPromoption1->points = 5;

        $cfCashedInPromoption2 = new \CultureFeed_Uitpas_Passholder_CashedInPointsPromotion();
        $cfCashedInPromoption2->id = 2;
        $date = new Date(
            new Year(2015),
            Month::AUGUST(),
            new MonthDay(27)
        );
        $cashingDate2 = $date->toNativeDateTime()->getTimestamp();
        $cfCashedInPromoption2->cashingDate = $cashingDate2;
        $cfCashedInPromoption2->title = 'Chocolate euro coin';
        $cfCashedInPromoption2->points = 10;

        $cfCashedInPromotionsCollection = array();

        $cfCashedInPromotionsCollection[] = $cfCashedInPromoption1;
        $cfCashedInPromotionsCollection[] = $cfCashedInPromoption2;

        $cfCashedInPromotions = new \CultureFeed_ResultSet(2, $cfCashedInPromotionsCollection);

        return $cfCashedInPromotions;
    }

    public function get_expected_data()
    {
        $expected1 = new CheckinPointsTransaction(
            new Date(
                new Year(2015),
                Month::DECEMBER(),
                new MonthDay(4)
            ),
            new StringLiteral('Event 4th of December'),
            new Integer(15)
        );

        $expected2 = new CashedPromotionPointsTransaction(
            new Date(
                new Year(2015),
                Month::AUGUST(),
                new MonthDay(27)
            ),
            new StringLiteral('Chocolate euro coin'),
            new Integer(10)
        );

        $expected3 = new CheckinPointsTransaction(
            new Date(
                new Year(2015),
                Month::JULY(),
                new MonthDay(11)
            ),
            new StringLiteral('Event 11th of July'),
            new Integer(5)
        );

        $expected4 = new CashedPromotionPointsTransaction(
            new Date(
                new Year(2015),
                Month::MARCH(),
                new MonthDay(21)
            ),
            new StringLiteral('Star wars stickers'),
            new Integer(5)
        );

        $expected5 = new CheckinPointsTransaction(
            new Date(
                new Year(2015),
                Month::JANUARY(),
                new MonthDay(21)
            ),
            new StringLiteral('Event 21st of January'),
            new Integer(10)
        );

        $expected = [
            $expected1,
            $expected2,
            $expected3,
            $expected4,
            $expected5,
        ];

        return $expected;
    }
}
