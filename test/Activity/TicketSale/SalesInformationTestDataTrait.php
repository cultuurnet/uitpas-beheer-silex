<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\Number\Real;

trait SalesInformationTestDataTrait
{
    use TariffTestDataTrait;

    /**
     * @return SalesInformation
     */
    public function getSampleSalesInformation()
    {
        $basePrices = (new Prices())
            ->withPricing(
                new PriceClass('Rang 1'),
                new Real(30)
            )
            ->withPricing(
                new PriceClass('Rang 2'),
                new Real(15)
            )
            ->withPricing(
                new PriceClass('Rang 3+'),
                new Real(7.5)
            );

        return new SalesInformation($basePrices);
    }

    /**
     * @return SalesInformation
     */
    public function getSampleSalesInformationWithTariffs()
    {
        return $this->getSampleSalesInformation()
            ->withTariff(
                $this->getSampleKansentariefTariff()
                    ->withMaximumReached(false)
            )
            ->withTariff(
                $this->getSampleCouponTariff()
                    ->withMaximumReached(true)
            );
    }
}
