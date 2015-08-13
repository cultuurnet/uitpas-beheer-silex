<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

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
            $this->getSamplePrices()
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
