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

class CheckinPointsTransactionServiceTest extends \PHPUnit_Framework_TestCase
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
    protected $service;

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
        date_default_timezone_set('Europe/Brussels');

        // Mock the system clock.
        $now = new DateTimeImmutable("2015-12-06", new DateTimeZone('Europe/Brussels'));
        $clock = new FrozenClock($now);
        $this->endTime = $clock->getDateTime()->getTimestamp();
        $this->startTime = strtotime("-1 year", $this->endTime);

        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new CheckinPointsTransactionService(
            $this->uitpas,
            $this->counterConsumerKey
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
    public function it_can_get_all_checkin_promotion_points_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $cfCheckinActivity1 = new \CultureFeed_Uitpas_Event_CheckinActivity();
        $cfCheckinActivity1->id = 1;
        $date = new Date(
            new Year(2015),
            Month::JULY(),
            new MonthDay(11)
        );
        $creationDate1 = $date->toNativeDateTime()->getTimestamp();
        $cfCheckinActivity1->creationDate = $creationDate1;
        $cfCheckinActivity1->nodeTitle = 'Title one.';
        $cfCheckinActivity1->points = 5;

        $cfCheckinActivity2 = new \CultureFeed_Uitpas_Event_CheckinActivity();
        $cfCheckinActivity2->id = 2;
        $date = new Date(
            new Year(2015),
            Month::DECEMBER(),
            new MonthDay(4)
        );
        $creationDate2 = $date->toNativeDateTime()->getTimestamp();
        $cfCheckinActivity2->creationDate = $creationDate2;
        $cfCheckinActivity2->nodeTitle = 'Title two.';
        $cfCheckinActivity2->points = 10;

        $cfCheckinActivityCollection = array();

        $cfCheckinActivityCollection[] = $cfCheckinActivity1;
        $cfCheckinActivityCollection[] = $cfCheckinActivity2;

        $cfCheckinActivities = new \CultureFeed_ResultSet(2, $cfCheckinActivityCollection);

        $expected1 = new CheckinPointsTransaction(
            new StringLiteral('1'),
            new Date(
                new Year(2015),
                Month::JULY(),
                new MonthDay(11)
            ),
            new StringLiteral('Title one.'),
            new Integer(5)
        );

        $expected2 = new CheckinPointsTransaction(
            new StringLiteral('2'),
            new Date(
                new Year(2015),
                Month::DECEMBER(),
                new MonthDay(4)
            ),
            new StringLiteral('Title two.'),
            new Integer(10)
        );

        $expected = [
            $expected1,
            $expected2,
        ];

        $startDateTime = \DateTime::createFromFormat('U', $this->startTime);
        $startDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $startDate = Date::fromNativeDateTime($startDateTime);

        $endDateTime = \DateTime::createFromFormat('U', $this->endTime);
        $endDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $endDate = Date::fromNativeDateTime($endDateTime);

        $expectedOptions = new \CultureFeed_Uitpas_Event_Query_SearchCheckinsOptions();
        $expectedOptions->balieConsumerKey = $this->counterConsumerKey->toNative();
        $expectedOptions->uid = $this->passHolder->uitIdUser->id;
        $expectedOptions->startDate = $this->startTime;
        $expectedOptions->endDate = $this->endTime;
        $expectedOptions->max = 100;
        $expectedOptions->checkinViaBalieConsumerKey = $this->counterConsumerKey->toNative();

        $this->uitpas->expects($this->once())
            ->method('searchCheckins')
            ->with($expectedOptions)
            ->willReturn($cfCheckinActivities);

        $checkins = $this->service->search($uitpasNumber, $startDate, $endDate);

        $this->assertEquals($expected, $checkins);
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_points_transactions_can_be_found()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $this->uitpas->expects($this->once())
            ->method('searchCheckins')
            ->willThrowException(new \CultureFeed_Exception('Not found.', 'not_found'));

        $startDateTime = \DateTime::createFromFormat('U', $this->startTime);
        $startDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $startDate = Date::fromNativeDateTime($startDateTime);

        $endDateTime = \DateTime::createFromFormat('U', $this->endTime);
        $endDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $endDate = Date::fromNativeDateTime($endDateTime);

        $checkins = $this->service->search($uitpasNumber, $startDate, $endDate);

        $this->assertNull($checkins);
    }
}
