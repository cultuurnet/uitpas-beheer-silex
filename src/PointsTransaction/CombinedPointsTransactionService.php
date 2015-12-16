<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;

class CombinedPointsTransactionService implements PointsTransactionServiceInterface
{
    /**
     * @var PointsTransactionServiceInterface[]
     */
    private $services;

    /**
     * CombinedPointsTransactionService constructor.
     */
    public function __construct()
    {
        $this->services = [];

        $arguments = func_get_args();
        foreach ($arguments as $argument) {
            $this->registerService($argument);
        }
    }

    public function registerService(PointsTransactionServiceInterface $service)
    {
        $this->services[] = $service;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Date $startDate
     * @param Date $endDate
     * @return PointsTransaction|null
     */
    public function search(UiTPASNumber $uitpasNumber, Date $startDate, Date $endDate)
    {
        $transactions = [];
        foreach ($this->services as $service) {
            $newTransactions = $service->search($uitpasNumber, $startDate, $endDate);
            if (!empty($newTransactions)) {
                $transactions = array_merge($transactions, $newTransactions);
            }
        }

        // Sort transactions.
        usort(
            $transactions,
            function ($item1, $item2) {
                $timestamp1 = $item1->getCreationDate()->toNativeDateTime()->getTimestamp();
                $timestamp2 = $item2->getCreationDate()->toNativeDateTime()->getTimestamp();
                return $timestamp2 - $timestamp1;
            }
        );

        return $transactions;
    }
}
