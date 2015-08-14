<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformation;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\TariffType;

class HasAvailableKansentarief implements SalesInformationSpecificationInterface
{
    /**
     * @param SalesInformation $salesInformation
     * @return bool
     */
    public static function isSatisfiedBy(SalesInformation $salesInformation)
    {
        // Try to find a kansentarief tariff that has not reached its maximum
        // number of sales.
        foreach ($salesInformation->getTariffs() as $tariff) {
            if ($tariff->getType()->is(TariffType::KANSENTARIEF()) &&
                !$tariff->hasReachedMaximum()) {
                return true;
            }
        }

        // No available kansentarief found.
        return false;
    }
}
