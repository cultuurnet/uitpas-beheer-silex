<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Specifications;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\SalesInformation;

class HasDifferentiation implements SalesInformationSpecificationInterface
{
    /**
     * @param SalesInformation $salesInformation
     * @return bool
     */
    public static function isSatisfiedBy(SalesInformation $salesInformation)
    {
        $amountOfPriceClasses = $salesInformation->getBasePrices()->count();
        $amountOfAvailableTariffs = 0;

        foreach ($salesInformation->getTariffs() as $tariff) {
            // A tariff is only available if the maximum number of sales for
            // that tariff has not been reached.
            if (!$tariff->hasReachedMaximum()) {
                $amountOfAvailableTariffs++;
            }
        }

        // There is price differentiation if there is at least one price class,
        // at least one available tariff, and either multiple price classes or
        // multiple available tariffs.
        // For example:
        // - 0 price classes and 0 available tariffs = no differentiation.
        // - 2 price classes and 0 available tariffs = no differentiation.
        // - 1 price class and 1 available tariff = no differentiation.
        // - 2 price classes and 1 available tariff = differentiation.
        // - 1 price class and 2 available tariffs = differentiation.
        // - 2 price classes and 2 available tariffs = differentiation.
        return $amountOfPriceClasses > 0 && $amountOfAvailableTariffs > 0 &&
            ($amountOfPriceClasses > 1 || $amountOfAvailableTariffs > 1);
    }
}
