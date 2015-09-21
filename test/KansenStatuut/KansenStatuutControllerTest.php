<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_MockObject_MockObject;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;

class KansenStatuutControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var KansenStatuutController
     */
    private $controller;

    /**
     * @var KansenStatuutServiceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $kansenStatuutService;

    public function setUp()
    {
        $this->kansenStatuutService = $this->getMock(KansenStatuutServiceInterface::class);

        $this->controller = new KansenStatuutController(
            $this->kansenStatuutService,
            new KansenStatuutEndDateJSONDeserializer()
        );
    }

    /**
     * @test
     */
    public function it_renews_a_kansen_statuut()
    {
        $data = '{"endDate": "2016-12-30"}';
        $request = new Request([], [], [], [], [], [], $data);

        $uitpasNumber = '0930000807113';
        $cardSystemId = '2';

        $this->kansenStatuutService->expects($this->once())
            ->method('renew')
            ->with(
                new UiTPASNumber($uitpasNumber),
                new CardSystemId($cardSystemId),
                new Date(
                    new Year('2016'),
                    Month::DECEMBER(),
                    new MonthDay(30)
                )
            );

        $this->controller->renew($request, $uitpasNumber, $cardSystemId);
    }
}
