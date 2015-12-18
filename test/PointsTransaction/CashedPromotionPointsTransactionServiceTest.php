<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

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

class CashedPromotionPointsTransactionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var CashedPromotionPointsTransactionService
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

        $this->service = new CashedPromotionPointsTransactionService(
            $this->uitpas,
            $this->counterConsumerKey
        );
    }

    /**
     * @test
     */
    public function it_can_get_all_cashed_promotion_points_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $cfCashedInPromoption1 = new \CultureFeed_Uitpas_Passholder_CashedInPointsPromotion();
        $cfCashedInPromoption1->id = 1;
        $date = new Date(
            new Year(2015),
            Month::JULY(),
            new MonthDay(11)
        );
        $cashingDate1 = $date->toNativeDateTime()->getTimestamp();
        $cfCashedInPromoption1->cashingDate = $cashingDate1;
        $cfCashedInPromoption1->title = 'Title one.';
        $cfCashedInPromoption1->points = 5;

        $cfCashedInPromoption2 = new \CultureFeed_Uitpas_Passholder_CashedInPointsPromotion();
        $cfCashedInPromoption2->id = 2;
        $date = new Date(
            new Year(2015),
            Month::DECEMBER(),
            new MonthDay(4)
        );
        $cashingDate2 = $date->toNativeDateTime()->getTimestamp();
        $cfCashedInPromoption2->cashingDate = $cashingDate2;
        $cfCashedInPromoption2->title = 'Title two.';
        $cfCashedInPromoption2->points = 10;

        $cfCashedInPromotionsCollection = array();

        $cfCashedInPromotionsCollection[] = $cfCashedInPromoption1;
        $cfCashedInPromotionsCollection[] = $cfCashedInPromoption2;

        $cfCashedInPromotions = new \CultureFeed_ResultSet(2, $cfCashedInPromotionsCollection);

        $expected1 = new CashedPromotionPointsTransaction(
            new StringLiteral('1'),
            new Date(
                new Year(2015),
                Month::JULY(),
                new MonthDay(11)
            ),
            new StringLiteral('Title one.'),
            new Integer(5)
        );

        $expected2 = new CashedPromotionPointsTransaction(
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

        $expectedOptions = new \CultureFeed_Uitpas_Passholder_Query_SearchCashedInPromotionPointsOptions();
        $expectedOptions->balieConsumerKey = $this->counterConsumerKey->toNative();
        $expectedOptions->uitpasNumber = $uitpasNumber->toNative();
        $expectedOptions->cashingPeriodBegin = $this->startTime;
        $expectedOptions->cashingPeriodEnd = $this->endTime;
        $expectedOptions->max = 20;
        //$expectedOptions->start = 0;

        $this->uitpas->expects($this->once())
            ->method('getCashedInPromotionPoints')
            ->with($expectedOptions)
            ->willReturn($cfCashedInPromotions);

        $cashedInPromotions = $this->service->search($uitpasNumber, $startDate, $endDate);

        $this->assertEquals($expected, $cashedInPromotions);
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_points_transactions_can_be_found()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $this->uitpas->expects($this->once())
            ->method('getCashedInPromotionPoints')
            ->willThrowException(new \CultureFeed_Exception('Not found.', 'not_found'));

        $startDateTime = \DateTime::createFromFormat('U', $this->startTime);
        $startDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $startDate = Date::fromNativeDateTime($startDateTime);

        $endDateTime = \DateTime::createFromFormat('U', $this->endTime);
        $endDateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $endDate = Date::fromNativeDateTime($endDateTime);

        $cashedInPromotions = $this->service->search($uitpasNumber, $startDate, $endDate);

        $this->assertNull($cashedInPromotions);
    }
}
