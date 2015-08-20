<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price;

use ValueObjects\Number\Real;

trait PricesTestDataTrait
{
    /**
     * @return Prices
     */
    public function getSamplePrices()
    {
        return (new Prices())
            ->withPricing(
                new PriceClass('Rang 1'),
                new Real(22)
            )
            ->withPricing(
                new PriceClass('Rang 2'),
                new Real(11)
            )
            ->withPricing(
                new PriceClass('Rang 3+'),
                new Real(5.5)
            );
    }

    /**
     * @return Prices
     */
    public function getSamplePricesWithMissingClass()
    {
        return (new Prices())
            ->withPricing(
                new PriceClass('Rang 1'),
                new Real(20)
            )
            ->withPricing(
                new PriceClass('Rang 3+'),
                new Real(5)
            );
    }
}
