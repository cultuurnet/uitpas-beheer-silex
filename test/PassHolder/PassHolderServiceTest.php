<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;

class PassHolderServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $counterConsumerKey;

    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var PassHolderService
     */
    protected $service;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('key');

        $this->service = new PassHolderService($this->uitpas, $this->counterConsumerKey);
    }

    /**
     * @test
     */
    public function it_can_get_a_passholder_by_identification_number()
    {
        $identification = 122345;

        $cardSystem = new \CultureFeed_Uitpas_CardSystem(1, 'uitpas');
        $cardSystem->id = $identification;

        $cardSystemSpecific = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystemSpecific->cardSystem = $cardSystem;

        $expected = new \CultureFeed_Uitpas_Passholder();
        $expected->name = 'Foo';
        $expected->cardSystemSpecific[1] = $cardSystemSpecific;

        $this->uitpas->expects($this->once())
            ->method('getPassholderByIdentificationNumber')
            ->with($identification)
            ->willReturn($expected);

        $actual = $this->service->getByIdentificationNumber($identification);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_passholder_cannot_be_found_by_identification_number()
    {
        $identification = 122345;

        $this->uitpas->expects($this->once())
            ->method('getPassholderByIdentificationNumber')
            ->with($identification)
            ->willThrowException(new \CultureFeed_Exception('Not found.', 404));

        $passholder = $this->service->getByIdentificationNumber($identification);

        $this->assertNull($passholder);
    }
}
