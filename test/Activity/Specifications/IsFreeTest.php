<?php

namespace CultuurNet\UiTPASBeheer\Activity\Specifications;

use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\CheckinConstraint;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Prices;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformation;
use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class IsFreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @var IsFree
     */
    protected $specification;

    public function setUp()
    {
        $this->activity = new Activity(
            new StringLiteral('123'),
            new StringLiteral('Test activity'),
            new CheckinConstraint(
                false,
                DateTime::fromNativeDateTime(new \DateTime('@0')),
                DateTime::fromNativeDateTime(new \DateTime('@0'))
            )
        );

        $this->specification = new IsFree();
    }

    /**
     * @test
     */
    public function it_is_satisfied_when_no_sales_information_is_set()
    {
        $this->assertTrue(
            $this->specification->isSatisfiedBy($this->activity)
        );
    }

    /**
     * @test
     */
    public function it_is_not_satisfied_when_at_least_one_of_the_base_prices_is_higher_than_zero()
    {
        $basePrices = (new Prices())
            ->withPricing(
                new PriceClass('Rang 1'),
                new Real(5.0)
            )
            ->withPricing(
                new PriceClass('Rang 2'),
                new Real(0)
            );

        $this->activity = $this->activity->withSalesInformation(
            new SalesInformation($basePrices)
        );

        $this->assertFalse(
            $this->specification->isSatisfiedBy($this->activity)
        );
    }

    /**
     * @test
     */
    public function it_is_satisfied_when_all_base_prices_are_zero()
    {
        $basePrices = (new Prices())
            ->withPricing(
                new PriceClass('Rang 1'),
                new Real(0)
            )
            ->withPricing(
                new PriceClass('Rang 2'),
                new Real(0)
            );

        $this->activity = $this->activity->withSalesInformation(
            new SalesInformation($basePrices)
        );

        $this->assertTrue(
            $this->specification->isSatisfiedBy($this->activity)
        );
    }
}
