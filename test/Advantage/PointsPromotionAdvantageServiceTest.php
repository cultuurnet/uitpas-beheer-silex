<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use DateTimeImmutable;
use DateTimeZone;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PointsPromotionAdvantageServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var PointsPromotionAdvantageService
     */
    protected $service;

    /**
     * @var int
     *
     * Current time, as a unix timestamp.
     */
    protected $currentTime;

    public function setUp()
    {
        // Mock the system clock.
        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Brussels'));
        $clock = new FrozenClock($now);
        $this->currentTime = $clock->getDateTime()->getTimestamp();

        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new PointsPromotionAdvantageService(
            $this->uitpas,
            $this->counterConsumerKey,
            $clock
        );
    }

    /**
     * @test
     */
    public function it_can_get_a_specific_points_promotion_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $id = new StringLiteral('10');
        $title = new StringLiteral('Quite the title.');
        $points = new Integer(5);
        $exchangeable = true;

        $cfAdvantage = new \CultureFeed_Uitpas_Passholder_PointsPromotion();
        $cfAdvantage->id = (int) $id->toNative();
        $cfAdvantage->title = $title->toNative();
        $cfAdvantage->points = $points->toNative();
        $cfAdvantage->cashInState = $cfAdvantage::CASHIN_POSSIBLE;

        $expected = new PointsPromotionAdvantage(
            $id,
            $title,
            $points,
            $exchangeable
        );

        $passHolderParameters = new \CultureFeed_Uitpas_Promotion_PassholderParameter();
        $passHolderParameters->uitpasNumber = $uitpasNumber->toNative();

        $this->uitpas->expects($this->once())
            ->method('getPointsPromotion')
            ->with($id->toNative(), $passHolderParameters)
            ->willReturn($cfAdvantage);

        $actual = $this->service->get($uitpasNumber, $id);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_specific_points_promotion_can_not_be_found()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $id = new StringLiteral('10');

        $this->uitpas->expects($this->once())
            ->method('getPointsPromotion')
            ->willThrowException(new \CultureFeed_Exception('Not found.', 'not_found'));

        $advantage = $this->service->get($uitpasNumber, $id);

        $this->assertNull($advantage);
    }

    /**
     * @test
     */
    public function it_can_get_all_exchangeable_points_promotions_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $cfAdvantage1 = new \CultureFeed_Uitpas_Passholder_PointsPromotion();
        $cfAdvantage1->id = 1;
        $cfAdvantage1->title = 'Title one.';
        $cfAdvantage1->points = 5;
        $cfAdvantage1->cashInState = $cfAdvantage1::CASHIN_POSSIBLE;

        $cfAdvantage2 = new \CultureFeed_Uitpas_Passholder_PointsPromotion();
        $cfAdvantage2->id = 2;
        $cfAdvantage2->title = 'Title two.';
        $cfAdvantage2->points = 10;
        $cfAdvantage2->cashInState = $cfAdvantage2::CASHIN_POSSIBLE;

        $cfAdvantages = new \CultureFeed_Uitpas_Passholder_PointsPromotionResultSet(
            2,
            [
                $cfAdvantage1,
                $cfAdvantage2,
            ]
        );

        $expected1 = new PointsPromotionAdvantage(
            new StringLiteral('1'),
            new StringLiteral('Title one.'),
            new Integer(5),
            true
        );

        $expected2 = new PointsPromotionAdvantage(
            new StringLiteral('2'),
            new StringLiteral('Title two.'),
            new Integer(10),
            true
        );

        $expected = [
            $expected1,
            $expected2,
        ];

        $expectedOptions = new \CultureFeed_Uitpas_Passholder_Query_SearchPromotionPointsOptions();
        $expectedOptions->balieConsumerKey = $this->counterConsumerKey->toNative();
        $expectedOptions->uitpasNumber = $uitpasNumber->toNative();
        $expectedOptions->unexpired = true;
        $expectedOptions->filterOnUserPoints = true;
        $expectedOptions->cashingPeriodBegin = $this->currentTime;
        $expectedOptions->cashingPeriodEnd = $this->currentTime;

        $this->uitpas->expects($this->once())
            ->method('getPromotionPoints')
            ->with($expectedOptions)
            ->willReturn($cfAdvantages);

        $advantages = $this->service->getExchangeable($uitpasNumber);

        $this->assertEquals($expected, $advantages);
    }

    /**
     * @test
     */
    public function it_can_exchange_a_points_promotion_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $id = new StringLiteral('10');

        $this->uitpas->expects($this->once())
            ->method('cashInPromotionPoints')
            ->with(
                $uitpasNumber->toNative(),
                $id->toNative(),
                $this->counterConsumerKey->toNative()
            );

        $this->service->exchange($uitpasNumber, $id);
    }
}
