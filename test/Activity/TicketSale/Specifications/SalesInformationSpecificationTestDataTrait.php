<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Prices;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Tariff;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\TariffType;
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
        $maximumReached = (bool) $maximumReached;

        return new Tariff(
            new StringLiteral('Cultuurnet Waardebon'),
            TariffType::COUPON(),
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(2.5)
                ),
            $maximumReached
        );
    }
}
