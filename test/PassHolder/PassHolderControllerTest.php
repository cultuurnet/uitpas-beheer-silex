<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use Symfony\Component\HttpFoundation\Request;

class PassHolderControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var PassHolderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var PassHolderController
     */
    protected $controller;

    public function setUp()
    {
        $this->service = $this->getMock(PassHolderServiceInterface::class);
        $this->controller = new PassHolderController($this->service);
    }

    /**
     * @test
     */
    public function it_responds_the_passholder_matching_a_provided_identification_number()
    {
        $identification = 122345;

        $cardSystem = new \CultureFeed_Uitpas_CardSystem(1, 'uitpas');
        $cardSystem->id = $identification;

        $cardSystemSpecific = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystemSpecific->cardSystem = $cardSystem;

        $passholder = new \CultureFeed_Uitpas_Passholder();
        $passholder->name = 'Foo';
        $passholder->cardSystemSpecific[1] = $cardSystemSpecific;

        $this->service->expects($this->once())
            ->method('getByIdentificationNumber')
            ->with($identification)
            ->willReturn($passholder);

        $request = new Request([], ['identification' => $identification]);
        $response = $this->controller->find($request);
        $json = $response->getContent();

        $this->assertJsonEquals($json, 'PassHolder/data/passholder.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_passholder_can_not_be_found()
    {
        $identification = 122345;

        $this->service->expects($this->once())
            ->method('getByIdentificationNumber')
            ->with($identification)
            ->willReturn(null);

        $request = new Request([], ['identification' => $identification]);

        try {
            $this->controller->find($request);
        } catch (PassHolderNotFoundException $exception) {
            $this->assertInstanceOf(ReadableCodeExceptionInterface::class, $exception);
            $this->assertNotEmpty($exception->getReadableCode());
        }
    }
}
