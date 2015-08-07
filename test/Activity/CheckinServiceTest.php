<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultureFeed_Uitpas_Passholder_Query_CheckInPassholderOptions;

class CheckinServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CheckinService
     */
    protected $checkinService;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->checkinService = new CheckinService(
            $this->uitpas,
            $this->counterConsumerKey
        );
    }

    /**
     * @test
     */
    public function it_should_return_the_saved_points_when_checking_in_a_passholder()
    {
        $uitpasNumber = new UiTPASNumber('0930000420206');
        $eventId = new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

        $checkinOptions = new CultureFeed_Uitpas_Passholder_Query_CheckInPassholderOptions();
        $checkinOptions->cdbid = 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee';
        $checkinOptions->uitpasNumber = '0930000420206';
        $checkinOptions->balieConsumerKey = 'key';

        $this->uitpas->expects($this->once())
            ->method('checkinPassholder')
            ->with($checkinOptions)
            ->willReturn(2);

        $points = $this->checkinService->checkin($uitpasNumber, $eventId);

        $this->assertEquals(2, $points);
    }
}
