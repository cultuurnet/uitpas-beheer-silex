<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ValueObjects\StringLiteral\StringLiteral;

final class CheckInCodeController
{
    /**
     * @var CheckInCodeService
     */
    private $service;

    public function __construct(CheckInCodeService $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $activityId
     * @param Request $request
     * @return StreamedResponse
     */
    public function download($activityId, Request $request)
    {
        $zipped = (bool) $request->get('zipped', false);

        $activityId = new StringLiteral($activityId);
        $download = $this->service->download($activityId, $zipped);

        $headers = $download->getHeaders();
        $stream = $download->getStream();

        $streamCallback = function () use ($stream) {
            $stream->rewind();
            while (!$stream->feof()) {
                print $stream->readLine();
            }
        };

        // Make sure the response isn't cached using setPrivate() and setClientTtl(),
        // because the codes can be different per request.
        return (new StreamedResponse($streamCallback, 200, $headers))
            ->setPrivate()
            ->setClientTtl(0);
    }
}
