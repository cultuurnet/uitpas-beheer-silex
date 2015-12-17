<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\Clock\Clock;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use ValueObjects\DateTime\Date;

class PointsTransactionController
{
    /**
     * @var PointsTransactionServiceInterface
     */
    protected $pointsTransactionService;

    /**
     * @var Clock
     */
    protected $clock;

    /**
     * PointsTransactionController constructor.
     * @param PointsTransactionServiceInterface $pointsTransactionService
     * @param Clock $clock
     */
    public function __construct(PointsTransactionServiceInterface $pointsTransactionService, Clock $clock)
    {
        $this->pointsTransactionService = $pointsTransactionService;
        $this->clock = $clock;
    }

    /**
     * @param $uitpasNumber
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getPointsTransactionsForPassholder($uitpasNumber)
    {
        $currentTime = $this->clock->getDateTime()->getTimestamp();
        $startTime = strtotime("-1 year", $currentTime);
        $endTime = strtotime("+1 day", $currentTime);

        $startDate = Date::fromNativeDateTime(
            \DateTime::createFromFormat('U', $startTime)
        );

        $endDate = Date::fromNativeDateTime(
            \DateTime::createFromFormat('U', $endTime)
        );

        $pointsTransactions = $this->pointsTransactionService->search(
            new UiTPASNumber($uitpasNumber),
            $startDate,
            $endDate
        );

        return JsonResponse::create()
            ->setData($pointsTransactions)
            ->setPrivate(true);
    }
}
