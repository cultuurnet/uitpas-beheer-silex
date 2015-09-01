<?php

namespace CultuurNet\UiTPASBeheer\Legacy\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class LegacyPassHolderServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed_Uitpas|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $uitpas;

    /**
     * @var CounterConsumerKey
     */
    protected $counterConsumerKey;

    /**
     * @var LegacyPassHolderService
     */
    protected $service;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    public function setUp()
    {
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class);
        $this->counterConsumerKey = new CounterConsumerKey('some-consumer-key');

        $this->service = new LegacyPassHolderService(
            $this->uitpas,
            $this->counterConsumerKey
        );

        $this->uitpasNumber = new UiTPASNumber('0930000420206');
    }

    /**
     * @test
     */
    public function it_can_return_a_culturefeed_passholder_by_uitpas_number()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->firstName = 'Foo';
        $cfPassHolder->name = 'Bar';
        $cfPassHolder->email = 'foo@bar.com';

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($this->uitpasNumber->toNative())
            ->willReturn($cfPassHolder);

        $this->assertEquals($cfPassHolder, $this->service->getByUiTPASNumber($this->uitpasNumber));
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_passholder_could_not_be_found()
    {
        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($this->uitpasNumber->toNative())
            ->willThrowException(
                new \CultureFeed_Exception(
                    'Passholder not found.',
                    'PASSHOLDER_NOT_FOUND'
                )
            );

        $this->assertNull($this->service->getByUiTPASNumber($this->uitpasNumber));
    }
}
