<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultureFeed_Uitpas;
use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use DateTimeImmutable;
use DateTimeZone;
use ValueObjects\StringLiteral\StringLiteral;

class WelcomeAdvantageServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var WelcomeAdvantageService
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

        $this->uitpas = $this->getMock(CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new WelcomeAdvantageService(
            $this->uitpas,
            $this->counterConsumerKey,
            $clock
        );
    }

    /**
     * @test
     */
    public function it_can_get_a_specific_welcome_advantage_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $id = new StringLiteral('10');
        $title = new StringLiteral('Quite the title.');
        $exchangeable = true;

        $cfAdvantage = new \CultureFeed_Uitpas_Passholder_WelcomeAdvantage();
        $cfAdvantage->id = (int) $id->toNative();
        $cfAdvantage->title = $title->toNative();
        $cfAdvantage->cashedIn = !$exchangeable;

        $expected = new WelcomeAdvantage(
            $id,
            $title,
            $exchangeable
        );

        $passHolderParameters = new \CultureFeed_Uitpas_Promotion_PassholderParameter();
        $passHolderParameters->uitpasNumber = $uitpasNumber->toNative();

        $this->uitpas->expects($this->once())
            ->method('getWelcomeAdvantage')
            ->with($id->toNative(), $passHolderParameters)
            ->willReturn($cfAdvantage);

        $actual = $this->service->get($uitpasNumber, $id);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_specific_welcome_advantage_can_not_be_found()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $id = new StringLiteral('10');

        $this->uitpas->expects($this->once())
            ->method('getWelcomeAdvantage')
            ->willThrowException(new \CultureFeed_Exception('Not found.', 'not_found'));

        $advantage = $this->service->get($uitpasNumber, $id);

        $this->assertNull($advantage);
    }

    /**
     * @test
     */
    public function it_can_get_all_exchangeable_welcome_advantages_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $cfAdvantage1 = new \CultureFeed_Uitpas_Passholder_WelcomeAdvantage();
        $cfAdvantage1->id = 1;
        $cfAdvantage1->title = 'Title one.';
        $cfAdvantage1->cashedIn = false;

        $cfAdvantage2 = new \CultureFeed_Uitpas_Passholder_WelcomeAdvantage();
        $cfAdvantage2->id = 2;
        $cfAdvantage2->title = 'Title two.';
        $cfAdvantage2->cashedIn = false;

        $cfAdvantages = new \CultureFeed_Uitpas_Passholder_WelcomeAdvantageResultSet(
            2,
            [
                $cfAdvantage1,
                $cfAdvantage2,
            ]
        );

        $expected1 = new WelcomeAdvantage(
            new StringLiteral('1'),
            new StringLiteral('Title one.'),
            true
        );

        $expected2 = new WelcomeAdvantage(
            new StringLiteral('2'),
            new StringLiteral('Title two.'),
            true
        );

        $expected = [
            $expected1,
            $expected2,
        ];

        $expectedOptions = new \CultureFeed_Uitpas_Passholder_Query_WelcomeAdvantagesOptions();
        $expectedOptions->balieConsumerKey = $this->counterConsumerKey->toNative();
        $expectedOptions->cashInBalieConsumerKey = $this->counterConsumerKey->toNative();

        // The property 'uitpas_number' is defined in the CultureFeed-PHP library, so there's no way for us to fix this
        // coding standards issue.
        // @codingStandardsIgnoreStart
        $expectedOptions->uitpas_number = $uitpasNumber->toNative();
        // @codingStandardsIgnoreEnd

        $expectedOptions->cashingPeriodBegin = $this->currentTime;
        $expectedOptions->cashingPeriodEnd = $this->currentTime;
        $expectedOptions->cashedIn = false;

        $this->uitpas->expects($this->once())
            ->method('getWelcomeAdvantagesForPassholder')
            ->with($expectedOptions)
            ->willReturn($cfAdvantages);

        $advantages = $this->service->getExchangeable($uitpasNumber);

        $this->assertEquals($expected, $advantages);
    }

    /**
     * @test
     */
    public function it_can_exchange_a_welcome_advantage_for_a_user()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');
        $id = new StringLiteral('10');

        $this->uitpas->expects($this->once())
            ->method('cashInWelcomeAdvantage')
            ->with(
                $uitpasNumber->toNative(),
                $id->toNative(),
                $this->counterConsumerKey->toNative()
            );

        $this->service->exchange($uitpasNumber, $id);
    }
}
