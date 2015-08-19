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
    public function getSampleInformationWithTariffs()
    {
        return $this->getSampleSalesInformation()
            ->withTariff(
                new Tariff(
                    new StringLiteral('Kansentarief'),
                    TariffType::KANSENTARIEF(),
                    $this->getSamplePrices()
                )
            )
            ->withTariff(
                new Tariff(
                    new StringLiteral('Cultuurwaardebon'),
                    TariffType::COUPON(),
                    $this->getSamplePrices(),
                    new StringLiteral('coupon-id-1')
                )
            );
    }

    /**
     * @return SalesInformation
     */
    public function getSampleSalesInformationWithUnsortedTariffs()
    {
        // Tariffs are intentionally not sorted, so we can test the sorting
        // when encoding to json.
        return $this->getSampleSalesInformation()
            ->withTariff(
                new Tariff(
                    new StringLiteral('Cultuurwaardebon 2'),
                    TariffType::COUPON(),
                    $this->getSamplePrices(),
                    new StringLiteral('coupon-id-2')
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
                    $this->getSamplePrices(),
                    new StringLiteral('coupon-id-1')
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

    /**
     * @return SalesInformation
     */
    public function getSampleSalesInformationWithMaximumReached()
    {
        return $this->getSampleSalesInformation()
            ->withTariff(
                (new Tariff(
                    new StringLiteral('Kansentarief'),
                    TariffType::KANSENTARIEF(),
                    $this->getSamplePrices()
                ))->withMaximumReached()
            );
    }
}
