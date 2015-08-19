<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

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
        // Tariffs are intentionally not sorted, so we can test the sorting
        // when encoding to json.
        return $this->getSampleSalesInformation()
            ->withTariff(
                new Tariff(
                    new StringLiteral('Cultuurwaardebon 2'),
                    TariffType::COUPON(),
                    $this->getSamplePrices()
                )
            )
            ->withTariff(
                new Tariff(
                    new StringLiteral('Kansentarief 1'),
                    TariffType::KANSENTARIEF(),
                    $this->getSamplePrices()
                )
            )
            ->withTariff(
                new Tariff(
                    new StringLiteral('Cultuurwaardebon 1'),
                    TariffType::COUPON(),
                    $this->getSamplePrices()
                )
            )
            ->withTariff(
                new Tariff(
                    new StringLiteral('Kansentarief 2'),
                    TariffType::KANSENTARIEF(),
                    $this->getSamplePrices()
                )
            );
    }
}
