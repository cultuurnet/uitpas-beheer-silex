<?php

namespace CultuurNet\UiTPASBeheer\Activity\SalesInformation\Specifications;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\Price\Prices;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\SalesInformation;
use ValueObjects\Number\Real;

class HasReachedMaximumSalesTest extends \PHPUnit_Framework_TestCase
{
    use SalesInformationSpecificationTestDataTrait;

    /**
     * @var SalesInformation
     */
    protected $salesInformation;

    /**
     * @var HasReachedMaximumSales
     */
    protected $specification;

    public function setUp()
    {
        $this->salesInformation = new SalesInformation(
            (new Prices())
                ->withPricing(
                    new PriceClass('Rang 1'),
                    new Real(10.0)
                )
        );

        $this->specification = new HasReachedMaximumSales();
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
    public function it_is_not_satisfied_when_at_least_one_tariff_has_not_reached_its_maximum_number_of_sales()
    {
        $this->salesInformation = $this->salesInformation
            ->withTariff($this->getUnavailableCouponTariff())
            ->withTariff($this->getAvailableCouponTariff())
            ->withTariff($this->getUnavailableCouponTariff());

        $this->assertFalse(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_it_satisfied_when_the_only_tariff_has_reached_its_maximum_number_of_sales()
    {
        $this->salesInformation = $this->salesInformation
            ->withTariff($this->getUnavailableCouponTariff());

        $this->assertTrue(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );
    }

    /**
     * @test
     */
    public function it_it_satisfied_when_all_tariffs_have_reached_their_maximum_number_of_sales()
    {
        $this->salesInformation = $this->salesInformation
            ->withTariff($this->getUnavailableCouponTariff())
            ->withTariff($this->getUnavailableCouponTariff())
            ->withTariff($this->getUnavailableCouponTariff());

        $this->assertTrue(
            $this->specification->isSatisfiedBy($this->salesInformation)
        );
    }
}
