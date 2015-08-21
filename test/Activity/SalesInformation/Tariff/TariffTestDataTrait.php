<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Tariff;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PricesTestDataTrait;
use ValueObjects\StringLiteral\StringLiteral;

trait TariffTestDataTrait
{
    use PricesTestDataTrait;

    /**
     * @return Tariff
     */
    public function getSampleCouponTariff()
    {
        return new Tariff(
            new StringLiteral('Cultuurwaardebon'),
            TariffType::COUPON(),
            $this->getSamplePrices(),
            new StringLiteral('coupon-id-1')
        );
    }

    /**
     * @return Tariff
     */
    public function getSampleKansentariefTariff()
    {
        return new Tariff(
            new StringLiteral('Kansentarief'),
            TariffType::KANSENTARIEF(),
            $this->getSamplePricesWithMissingClass()
        );
    }
}
