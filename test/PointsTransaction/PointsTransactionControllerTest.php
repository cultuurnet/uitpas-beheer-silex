<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use DateTimeImmutable;
use DateTimeZone;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PointsTransactionControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PointsTransactionController
     */
    protected $controller;

    /**
     * @var CombinedPointsTransactionService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var FrozenClock
     */
    protected $clock;

    public function setUp()
    {
        $now = DateTimeImmutable::createFromFormat('U', '1449237174', new DateTimeZone('Europe/Brussels'));
        $this->clock = new FrozenClock($now);
        $this->service = $this->getMock(CombinedPointsTransactionService::class);
        $this->controller = new PointsTransactionController($this->service, $this->clock);
    }

    /**
     * @test
     */
    public function it_responds_the_points_transactions_for_passholder()
    {
        $uitpasNumber = new UiTPASNumber('0930000125607');

        $currentTime = $this->clock->getDateTime()->getTimestamp();
        $startTime = strtotime("-1 year", $currentTime);
        $endTime = strtotime("+1 day", $currentTime);

        $startDateTime = \DateTime::createFromFormat('U', $startTime);
        $startDateTime->setTimezone(new \DateTimeZone('Europe/Brussels'));
        $startDate = Date::fromNativeDateTime($startDateTime);

        $endDateTime = \DateTime::createFromFormat('U', $endTime);
        $endDateTime->setTimezone(new \DateTimeZone('Europe/Brussels'));
        $endDate = Date::fromNativeDateTime($endDateTime);

        $checkin3 = new CheckinPointsTransaction(
            new StringLiteral('35'),
            new Date(
                new Year(2015),
                Month::DECEMBER(),
                new MonthDay(4)
            ),
            new StringLiteral('Event 4th of December'),
            new Integer(10)
        );
        $cashedPromotion1 = new CashedPromotionPointsTransaction(
            new StringLiteral('16'),
            new Date(
                new Year(2015),
                Month::JUNE(),
                new MonthDay(23)
            ),
            new StringLiteral('Cake for points'),
            new Integer(15)
        );

        $checkin2  = new CheckinPointsTransaction(
            new StringLiteral('23'),
            new Date(
                new Year(2015),
                Month::MAY(),
                new MonthDay(13)
            ),
            new StringLiteral('Event 13th of May'),
            new Integer(20)
        );
        $checkin1  = new CheckinPointsTransaction(
            new StringLiteral('12'),
            new Date(
                new Year(2015),
                Month::JANUARY(),
                new MonthDay(21)
            ),
            new StringLiteral('Event 21st of January'),
            new Integer(15)
        );

        $pointsTransactions = [
            $checkin3,
            $cashedPromotion1,
            $checkin2,
            $checkin1,
        ];

        $this->service->expects($this->once())
            ->method('search')
            ->with($uitpasNumber, $startDate, $endDate)
            ->willReturn($pointsTransactions);

        $response = $this->controller->getPointsTransactionsForPassholder($uitpasNumber->toNative());
        $content = $response->getContent();

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/data/pointsTransactions.json',
            $content
        );
    }
}
