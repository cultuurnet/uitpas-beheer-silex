<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Specifications;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\Prices;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\SalesInformation;
use ValueObjects\Number\Real;

/**
 * Requirements for satisfaction:
 * Multiple base prices and at least one tariff which has not reached its
 * maximum number of sales, OR multiple tariffs which have not reached their
 * maximum number of sales and at least one base price.
 */
class HasDifferentiationTest extends \PHPUnit_Framework_TestCase
{
    use SalesInformationSpecificationTestDataTrait;

    /**
     * @var HasDifferentiation
     */
    protected $specification;

    public function setUp()
    {
        $this->specification = new HasDifferentiation();
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_no_base_prices_and_no_tariffs()
    {
        $salesInformation = new SalesInformation(new Prices());

        $this->assertFalse(
            $this->specification->isSatisfiedBy($salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_a_single_base_price_and_a_single_available_tariff()
    {
        $salesInformation = new SalesInformation(
            $this->getSinglePrice()
        );

        $salesInformation = $salesInformation->withTariff(
            $this->getAvailableKansentariefTariff()
        );

        $this->assertFalse(
            $this->specification->isSatisfiedBy($salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_multiple_base_prices_and_no_tariffs()
    {
        $salesInformation = new SalesInformation(
            $this->getMultiplePrices()
        );

        $this->assertFalse(
            $this->specification->isSatisfiedBy($salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_by_multiple_base_prices_and_only_unavailable_tariffs()
    {
        $salesInformation = new SalesInformation(
            $this->getMultiplePrices()
        );

        $salesInformation = $salesInformation
            ->withTariff($this->getUnavailableCouponTariff())
            ->withTariff($this->getUnavailableCouponTariff());

        $this->assertFalse(
            $this->specification->isSatisfiedBy($salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_a_single_base_price_and_multiple_available_tariffs()
    {
        $salesInformation = new SalesInformation(
            $this->getSinglePrice()
        );

        /* @var \CultuurNet\UiTPASBeheer\Activity\SalesInformation\SalesInformation $salesInformation */
        // Third tariff is unavailable on purpose.
        $salesInformation = $salesInformation
            ->withTariff($this->getAvailableKansentariefTariff())
            ->withTariff($this->getAvailableCouponTariff())
            ->withTariff($this->getUnavailableCouponTariff());

        $this->assertTrue(
            $this->specification->isSatisfiedBy($salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_multiple_base_prices_and_a_single_available_tariff()
    {
        $salesInformation = new SalesInformation(
            $this->getMultiplePrices()
        );

        $salesInformation = $salesInformation->withTariff(
            $this->getAvailableKansentariefTariff()
        );

        $this->assertTrue(
            $this->specification->isSatisfiedBy($salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_by_multiple_base_prices_and_multiple_available_tariffs()
    {
        $salesInformation = new SalesInformation(
            $this->getMultiplePrices()
        );

        /* @var SalesInformation $salesInformation */
        // Third tariff is unavailable on purpose.
        $salesInformation = $salesInformation
            ->withTariff($this->getAvailableKansentariefTariff())
            ->withTariff($this->getAvailableCouponTariff())
            ->withTariff($this->getUnavailableCouponTariff());

        $this->assertTrue(
            $this->specification->isSatisfiedBy($salesInformation)
        );
    }

    /**
     * @return Prices
     */
    private function getSinglePrice()
    {
        return (new Prices())
            ->withPricing(
                new PriceClass('Rang 1'),
                new Real(10.0)
            );
    }

    /**
     * @return \CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\Prices
     */
    private function getMultiplePrices()
    {
        return $this->getSinglePrice()
            ->withPricing(
                new PriceClass('Rang 2'),
                new Real(5.0)
            );
    }
}
