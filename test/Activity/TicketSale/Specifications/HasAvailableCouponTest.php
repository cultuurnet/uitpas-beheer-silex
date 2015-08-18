<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Specifications;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Prices;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformation;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Tariff;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\TariffType;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class HasAvailableCouponTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SalesInformation
     */
    protected $salesInformation;

    /**
     * @var HasAvailableCoupon
     */
    protected $specification;

    public function setUp()
    {
        $this->salesInformation = new SalesInformation(
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(5.0)
                )
                ->withPricing(
                    new PriceClass('Rang 2'),
                    new Real(3.0)
                )
        );

        $this->specification = new HasAvailableCoupon();
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_when_no_tariffs_are_set()
    {
        $this->assertFalse(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_an_available_kansentarief_tariff()
    {
        $tariff = new Tariff(
            new StringLiteral('Kansentarief'),
            TariffType::KANSENTARIEF(),
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(2.5)
                )
                ->withPricing(
                    new PriceClass('Rang 2'),
                    new Real(1.5)
                )
        );

        $this->salesInformation = $this->salesInformation->withTariff($tariff);

        $this->assertFalse(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_when_at_least_one_coupon_tariff_is_found_which_has_not_reached_maximum_sales()
    {
        // One coupon found, but it has reached its maximum number of sales.
        $maximumReachedTariff = $this->getSampleCouponTariffWithMaximumReached(true);
        $this->salesInformation = $this->salesInformation->withTariff($maximumReachedTariff);
        $this->assertFalse(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );

        // A second coupon, which has not reached its maximum number of sales.
        $availableTariff = $this->getSampleCouponTariffWithMaximumReached(false);
        $this->salesInformation = $this->salesInformation->withTariff($availableTariff);
        $this->assertTrue(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );
    }

    /**
     * @param $maximumReached
     * @return Tariff
     */
    private function getSampleCouponTariffWithMaximumReached($maximumReached)
    {
        $maximumReached = (bool) $maximumReached;

        return new Tariff(
            new StringLiteral('Cultuurnet Waardebon'),
            TariffType::COUPON(),
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(2.5)
                )
                ->withPricing(
                    new PriceClass('Rang 2'),
                    new Real(1.5)
                ),
            $maximumReached
        );
    }
}
