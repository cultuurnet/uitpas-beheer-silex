<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\UiTPASBeheer\Http\ContentDispositionHeader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ValueObjects\StringLiteral\StringLiteral;

final class CheckInCodeController
{
    /**
     * @var CheckInCodeServiceInterface
     */
    private $service;

    public function __construct(CheckInCodeServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $activityId
     * @param string $fileName
     * @return StreamedResponse
     */
    public function downloadPdf($activityId, $fileName)
    {
        $activityId = new StringLiteral($activityId);
        $download = $this->service->download($activityId, false);
        return $this->convertDownloadToStreamedResponse($download, $fileName . '.pdf');
    }

    /**
     * @param string $activityId
     * @param string $fileName
     * @return StreamedResponse
     */
    public function downloadZip($activityId, $fileName)
    {
        $activityId = new StringLiteral($activityId);
        $download = $this->service->download($activityId, true);
        return $this->convertDownloadToStreamedResponse($download, $fileName . '.zip');
    }

    /**
     * @param CheckInCodeDownload $download
     * @param string $fileName
     * @return StreamedResponse
     */
    private function convertDownloadToStreamedResponse(CheckInCodeDownload $download, $fileName)
    {
        $download = $download->withContentDispositionHeader(
            ContentDispositionHeader::fromFileName($fileName)
        );

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
