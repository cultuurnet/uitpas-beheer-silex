<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Specifications;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\Prices;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Tariff\Tariff;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Tariff\TariffType;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

trait SalesInformationSpecificationTestDataTrait
{
    /**
     * @return Tariff
     */
    public function getAvailableCouponTariff()
    {
        return $this->getCouponTariff(false);

    }

    /**
     * @return Tariff
     */
    public function getUnavailableCouponTariff()
    {
        return $this->getCouponTariff(true);
    }

    /**
     * @param bool $maximumReached
     * @return Tariff
     */
    public function getCouponTariff($maximumReached)
    {
        return (new Tariff(
            new StringLiteral('Cultuurnet Waardebon'),
            TariffType::COUPON(),
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(2.5)
                ),
            new StringLiteral('coupon-id-1')
        ))->withMaximumReached((bool) $maximumReached);
    }

    /**
     * @return Tariff
     */
    public function getAvailableKansentariefTariff()
    {
        return new Tariff(
            new StringLiteral('Kansentarief'),
            TariffType::KANSENTARIEF(),
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(5.0)
                )
        );
    }
}
