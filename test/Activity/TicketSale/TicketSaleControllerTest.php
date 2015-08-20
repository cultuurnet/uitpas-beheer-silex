<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\RegistrationJsonDeserializer;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
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

class TicketSaleControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var TicketSaleController
     */
    protected $controller;

    /**
     * @var TicketSaleService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $registrationJsonDeserializer;

    public function setUp()
    {
        $callOriginalConstructor = false;
        $this->service = $this->getMock(TicketSaleService::class, [], [], '', $callOriginalConstructor);

        $this->registrationJsonDeserializer = new RegistrationJsonDeserializer();

        $this->controller = new TicketSaleController(
            $this->service,
            $this->registrationJsonDeserializer
        );
    }

    /**
     * @test
     */
    public function it_responds_with_a_new_ticket_sale_when_registering()
    {
        $uitpasNumber = new UiTPASNumber('1000000600717');

        $registrationJson = file_get_contents(__DIR__ . '/../data/ticket-sale/registration.json');

        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($registrationJson)
        );

        $this->service->expects($this->once())
            ->method('register')
            ->with(
                $uitpasNumber,
                $registration
            )
            ->willReturn(
                new TicketSale(
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
                )
            );

        $request = new Request([], [], [], [], [], [], $registrationJson);
        $response = $this->controller->register(
            $request,
            $uitpasNumber->toNative()
        );

        $actualTicketSaleJson = $response->getContent();

        $this->assertJsonEquals($actualTicketSaleJson, 'Activity/data/ticket-sale/ticket-sale-minimal.json');
    }
}
