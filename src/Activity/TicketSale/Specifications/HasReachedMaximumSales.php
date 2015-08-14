<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformation;

class HasReachedMaximumSales implements SalesInformationSpecificationInterface
{
    /**
     * @param SalesInformation $salesInformation
     * @return bool
     */
    public static function isSatisfiedBy(SalesInformation $salesInformation)
    {
        $tariffs = $salesInformation->getTariffs();

        // We have no way of knowing whether the maximum of sales has been
        // reached, so we assume it hasn't been reached.
        if (empty($tariffs)) {
            return false;
        }

        // As soon as we find a tariff that has not reached its maximum number
        // of sales, the total maximum has not been reached.
        foreach ($tariffs as $tariff) {
            if (!$tariff->hasReachedMaximum()) {
                return false;
            }
        }

        // All tariffs have reached their maximum number of sales.
        return true;
    }
}
