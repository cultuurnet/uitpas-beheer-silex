<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use CultuurNet\UiTPASBeheer\Properties\DateRangeJsonDeserializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ValueObjects\StringLiteral\StringLiteral;

class ExpenseReportController
{
    /**
     * @var ExpenseReportServiceInterface
     */
    private $service;

    /**
     * @var ExpenseReportApiServiceInterface
     */
    private $api;

    /**
     * @var DateRangeJsonDeserializer
     */
    private $dateRangeJsonDeserializer;

    /**
     * @param ExpenseReportServiceInterface $service
     * @param ExpenseReportApiServiceInterface $api
     * @param DateRangeJsonDeserializer $dateRangeJsonDeserializer
     */
    public function __construct(
        ExpenseReportServiceInterface $service,
        ExpenseReportApiServiceInterface $api,
        DateRangeJsonDeserializer $dateRangeJsonDeserializer
    ) {
        $this->service = $service;
        $this->api = $api;
        $this->dateRangeJsonDeserializer = $dateRangeJsonDeserializer;
    }

    /**
     * @return JsonResponse
     */
    public function getPeriods()
    {
        $periods = $this->service->getPeriods();

        return (new JsonResponse())
            ->setData($periods)
            ->setPrivate();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generate(Request $request)
    {
        $dateRange = $this->dateRangeJsonDeserializer->deserialize(
            new StringLiteral(
                $request->getContent()
            )
        );

        $info = $this->service->generate($dateRange);

        return (new JsonResponse())
            ->setData($info)
            ->setPrivate();
    }

    /**
     * @param string $expenseReportId
     * @return JsonResponse
     */
    public function getStatus($expenseReportId)
    {
        $expenseReportId = new ExpenseReportId($expenseReportId);

        $status = $this->service->getStatus($expenseReportId);

        return (new JsonResponse())
            ->setData($status)
            ->setPrivate();
    }

    /**
     * @param string $expenseReportId
     * @return StreamedResponse
     */
    public function download($expenseReportId)
    {
        $expenseReportId = new ExpenseReportId($expenseReportId);
        $download = $this->api->download($expenseReportId);

        $headers = $download->getHeaders();
        $stream = $download->getStream();

        $streamCallback = function () use ($stream) {
            $stream->rewind();
            while (!$stream->feof()) {
                print $stream->readLine();
            }
        };

        return new StreamedResponse($streamCallback, 200, $headers);
    }
}
