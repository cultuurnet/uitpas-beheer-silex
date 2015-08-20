<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Specifications;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\Prices;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\SalesInformation;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Tariff\Tariff;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Tariff\TariffType;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class HasAvailableKansentariefTest extends \PHPUnit_Framework_TestCase
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

        $this->specification = new HasAvailableKansentarief();
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
    public function it_is_not_satisfied_by_an_available_coupon_tariff()
    {
        $tariff = new Tariff(
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
            new StringLiteral('coupon-id-1')
        );

        $this->salesInformation = $this->salesInformation->withTariff($tariff);

        $this->assertFalse(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_when_a_kansentarief_tariff_is_found_which_has_not_reached_maximum_sales()
    {
        // Kansentarief exists, but it has reached its maximum number of sales.
        $maximumReachedTariff = $this->getSampleKansentariefTariffWithMaximumReached(true);
        $this->assertFalse(
            $this->specification->isSatisfiedBy(
                $this->salesInformation->withTariff($maximumReachedTariff)
            )
        );

        // Kansentarief which has not reached its maximum number of sales.
        $availableTariff = $this->getSampleKansentariefTariffWithMaximumReached(false);
        $this->assertTrue(
            $this->specification->isSatisfiedBy(
                $this->salesInformation->withTariff($availableTariff)
            )
        );
    }

    /**
     * @param $maximumReached
     * @return Tariff
     */
    private function getSampleKansentariefTariffWithMaximumReached($maximumReached)
    {
        $maximumReached = (bool) $maximumReached;

        return (new Tariff(
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
        ))->withMaximumReached($maximumReached);
    }
}
