<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\DateTime;
use ValueObjects\DateTime\Hour;
use ValueObjects\DateTime\Minute;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Second;
use ValueObjects\DateTime\Time;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class TicketSaleServiceTest extends \PHPUnit_Framework_TestCase
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
     * @var TicketSaleService
     */
    protected $service;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    /**
     * @var Registration
     */
    protected $registration;

    /**
     * @var StringLiteral
     */
    protected $tariffId;

    /**
     * @var \CultureFeed_Uitpas_Event_TicketSale
     */
    protected $cfTicketSale;

    public function setUp()
    {
        $callOriginalConstructor = false;
        $this->uitpas = $this->getMock(\CultureFeed_Uitpas::class, [], [], '', $callOriginalConstructor);

        $this->counterConsumerKey = new CounterConsumerKey('foo-bar');

        $this->service = new TicketSaleService(
            $this->uitpas,
            $this->counterConsumerKey
        );

        $this->uitpasNumber = new UiTPASNumber('1000000600717');

        $this->registration = new Registration(
            new StringLiteral('100'),
            new PriceClass('Basisprijs')
        );

        $this->tariffId = new StringLiteral('coupon-id-1');

        $this->cfTicketSale = new \CultureFeed_Uitpas_Event_TicketSale();
        $this->cfTicketSale->id = 30818;
        $this->cfTicketSale->price = 2;
        $this->cfTicketSale->creationDate = 1440079102;
    }

    /**
     * @test
     */
    public function it_registers_a_ticket_sale()
    {
        $this->uitpas->expects($this->once())
            ->method('registerTicketSale')
            ->with(
                $this->uitpasNumber->toNative(),
                $this->registration->getActivityId()->toNative(),
                $this->counterConsumerKey->toNative(),
                $this->registration->getPriceClass()->toNative(),
                null,
                null
            )
            ->willReturn($this->cfTicketSale);

        $expected = new TicketSale(
            new StringLiteral('30818'),
            new Real(2.0),
            new DateTime(
                new Date(
                    new Year(2015),
                    Month::getByName('AUGUST'),
                    new MonthDay('20')
                ),
                new Time(
                    new Hour(13),
                    new Minute(58),
                    new Second(22)
                )
            )
        );

        $actual = $this->service->register(
            $this->uitpasNumber,
            $this->registration
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_uses_the_tariff_id_if_provided_when_registering_a_ticket_sale()
    {
        $this->registration = $this->registration
            ->withTariffId($this->tariffId);

        $this->uitpas->expects($this->once())
            ->method('registerTicketSale')
            ->with(
                $this->uitpasNumber->toNative(),
                $this->registration->getActivityId()->toNative(),
                $this->counterConsumerKey->toNative(),
                $this->registration->getPriceClass()->toNative(),
                $this->tariffId->toNative(),
                null
            )
            ->willReturn($this->cfTicketSale);

        $this->service->register(
            $this->uitpasNumber,
            $this->registration
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_ticket_sale_registration_failed()
    {
        $expectedMessage = 'Pashouder ticket sales:1. Toegelaten ticketSales: 1 / Absoluut';
        $expectedCode = 'MAXIMUM_REACHED';

        $cfException = new \CultureFeed_Exception(
            $expectedMessage,
            $expectedCode
        );

        $this->uitpas->expects($this->once())
            ->method('registerTicketSale')
            ->willThrowException($cfException);

        // We can't use setExpectedException here because we need to check the
        // readable error code.
        try {
            $this->service->register(
                $this->uitpasNumber,
                $this->registration
            );
            $this->fail('A ReadableCodeResponseException should have been thrown.');
        } catch (ReadableCodeResponseException $e) {
            $this->assertEquals($expectedMessage, $e->getMessage());
            $this->assertEquals($expectedCode, $e->getReadableCode());
            $this->assertEquals(400, $e->getCode());
        } catch (\Exception $e) {
            $this->fail(
                'A ReadableCodeResponseException should have been thrown. Caught ' . get_class($e) . ' instead.'
            );
        }
    }
}
