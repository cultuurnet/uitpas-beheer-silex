<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

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
    public function it_can_get_a_passholder_by_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Jette (Brussel)';
        $cfPassHolder->dateOfBirth = 208742400;

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willReturn($cfPassHolder);

        $expected = PassHolder::fromCultureFeedPassHolder($cfPassHolder);
        $actual = $this->service->getByUitpasNumber($uitpasNumber);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_passholder_cannot_be_found_by_uitpas_number()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $this->uitpas->expects($this->once())
            ->method('getPassholderByUitpasNumber')
            ->with($uitpasNumberValue)
            ->willThrowException(new \CultureFeed_Exception('Not found.', 404));

        $passholder = $this->service->getByUitpasNumber($uitpasNumber);

        $this->assertNull($passholder);
    }

    /**
     * @test
     */
    public function it_updates_a_given_passholder()
    {
        $uitpasNumberValue = '0930000125607';
        $uitpasNumber = new UiTPASNumber($uitpasNumberValue);

        $passHolder = new \CultureFeed_Uitpas_Passholder();
        $passHolder->name = 'Foo';

        $this->uitpas->expects($this->once())
            ->method('updatePassholder')
            ->with($passHolder, $this->counterConsumerKey);

        $this->service->update($uitpasNumber, $passHolder);
    }
}
