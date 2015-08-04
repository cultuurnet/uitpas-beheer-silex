<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;

class IdentityServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cfUitpasService;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var IdentityService
     */
    protected $service;

    /**
     * @var string
     */
    protected $identification;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    public function setUp()
    {
        $this->cfUitpasService = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new IdentityService(
            $this->cfUitpasService,
            $this->counterConsumerKey
        );

        $this->identification = '1234567890';

        $this->uitpasNumber = new UiTPASNumber('0930000237915');

    }

    /**
     * @test
     */
    public function it_can_get_an_identity_based_on_an_identification_number()
    {
        $cfPassHolderCard = new \CultureFeed_Uitpas_Passholder_Card();
        $cfPassHolderCard->uitpasNumber = $this->uitpasNumber->toNative();
        $cfPassHolderCard->kansenpas = $this->uitpasNumber->hasKansenStatuut();
        $cfPassHolderCard->status = UiTPASStatus::LOCAL_STOCK();

        $cfIdentity = new \CultureFeed_Uitpas_Identity();
        $cfIdentity->card = $cfPassHolderCard;

        $this->cfUitpasService->expects($this->once())
            ->method('identify')
            ->with(
                $this->identification,
                $this->counterConsumerKey->toNative()
            )
            ->willReturn($cfIdentity);

        $uitpas = new UiTPAS(
            $this->uitpasNumber,
            UiTPASStatus::LOCAL_STOCK()
        );

        $expected = new Identity($uitpas);
        $actual = $this->service->get($this->identification);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_identity_could_be_found()
    {
        $this->cfUitpasService->expects($this->once())
            ->method('identify')
            ->with(
                $this->identification,
                $this->counterConsumerKey->toNative()
            )
            ->willThrowException(new \CultureFeed_Exception('Not found.', 'NOT_FOUND'));

        $this->assertNull(
            $this->service->get($this->identification)
        );
    }
}
