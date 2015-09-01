<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;

class IdentityControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var IdentityServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    /**
     * @var IdentityController
     */
    protected $controller;

    /**
     * @var string
     */
    protected $identification;

    public function setUp()
    {
        $this->service = $this->getMock(IdentityServiceInterface::class);
        $this->controller = new IdentityController($this->service);

        $this->identification = '1000000035419';
    }

    /**
     * @test
     */
    public function it_can_respond_an_identity_based_on_an_identification_number()
    {
        $identity = new Identity(
            new UiTPAS(
                new UiTPASNumber($this->identification),
                UiTPASStatus::LOCAL_STOCK(),
                UiTPASType::CARD(),
                new CardSystemId('999')
            )
        );

        $this->service->expects($this->once())
            ->method('get')
            ->with($this->identification)
            ->willReturn($identity);

        $response = $this->controller->get($this->identification);

        $json = $response->getContent();
        $this->assertJsonEquals($json, 'Identity/data/identity-minimum.json');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_an_identity_could_not_be_found()
    {
        $this->service->expects($this->once())
            ->method('get')
            ->with($this->identification)
            ->willReturn(null);

        $this->setExpectedException(IdentityNotFoundException::class);

        $this->controller->get($this->identification);
    }
}
