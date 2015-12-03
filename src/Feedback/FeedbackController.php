<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ValueObjects\StringLiteral\StringLiteral;

class FeedbackController
{
    /**
     * @var FeedbackServiceInterface
     */
    private $service;

    /**
     * @var FeedbackJsonDeserializer
     */
    private $feedbackJsonDeserializer;

    /**
     * @param FeedbackServiceInterface $service
     * @param FeedbackJsonDeserializer $feedbackJsonDeserializer
     */
    public function __construct(
        FeedbackServiceInterface $service,
        FeedbackJsonDeserializer $feedbackJsonDeserializer
    ) {
        $this->service = $service;
        $this->feedbackJsonDeserializer = $feedbackJsonDeserializer;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function send(Request $request)
    {
        $data = $request->getContent();

        $feedback = $this->feedbackJsonDeserializer->deserialize(
            new StringLiteral($data)
        );

        $this->service->send($feedback);

        return new Response();
    }
}
