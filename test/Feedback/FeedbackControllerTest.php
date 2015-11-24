<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use ValueObjects\StringLiteral\StringLiteral;

class FeedbackControllerTest extends \PHPUnit_Framework_TestCase
{
    use FeedbackTestDataTrait;

    /**
     * @var FeedbackServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $service;

    /**
     * @var FeedbackJsonDeserializer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $jsonDeserializer;

    /**
     * @var FeedbackController
     */
    private $controller;

    public function setUp()
    {
        $this->service = $this->getMock(FeedbackServiceInterface::class);
        $this->jsonDeserializer = $this->getMock(FeedbackJsonDeserializer::class);

        $this->controller = new FeedbackController(
            $this->service,
            $this->jsonDeserializer
        );
    }

    /**
     * @test
     */
    public function it_responds_when_sending_feedback()
    {
        $json = file_get_contents(__DIR__ . '/data/feedback.json');
        $request = new Request([], [], [], [], [], [], $json);

        $this->jsonDeserializer->expects($this->once())
            ->method('deserialize')
            ->with(new StringLiteral($json))
            ->willReturn($this->getFeedback());

        $this->service->expects($this->once())
            ->method('send')
            ->with($this->getFeedback());

        $response = $this->controller->send($request);

        $this->assertInstanceOf(Response::class, $response);
    }
}
