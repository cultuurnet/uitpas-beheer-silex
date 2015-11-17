<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\RegisteredTicketSale;
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
    use TicketSaleTestDataTrait;

    /**
     * @var TicketSaleController
     */
    protected $controller;

    /**
     * @var TicketSaleServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $registrationJsonDeserializer;

    public function setUp()
    {
        date_default_timezone_set('UTC');

        $this->service = $this->getMock(TicketSaleServiceInterface::class);
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
                new RegisteredTicketSale(
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

        $this->assertJsonEquals($actualTicketSaleJson, 'Activity/data/ticket-sale/registered-ticket-sale.json');
    }

    /**
     * @test
     */
    public function it_responds_with_a_list_of_ticket_sales_when_searching_by_uitpas_number()
    {
        $uitpasNumber = new UiTPASNumber('1000000600717');

        $this->service->expects($this->once())
            ->method('getByUiTPASNumber')
            ->with($uitpasNumber)
            ->willReturn($this->getTicketSaleHistory());

        $response = $this->controller->getByUiTPASNumber($uitpasNumber->toNative());

        $json = $response->getContent();
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/history.json');
    }

    /**
     * @test
     */
    public function it_responds_with_a_boolean_when_cancelling_a_ticket()
    {
        $ticketId = new StringLiteral('123');

        $this->service->expects($this->once())
            ->method('cancel')
            ->with($ticketId)
            ->willReturn(true);

        $response = $this->controller->cancel($ticketId->toNative());

        $json = $response->getContent();
        $this->assertEquals($json, 'true');
    }
}
