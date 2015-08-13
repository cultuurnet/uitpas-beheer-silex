<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\Number\Real;

class PricesTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;
    use PricesTestDataTrait;

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->getSamplePrices());
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/prices.json');
    }

    /**
     * @test
     */
    public function it_can_check_if_it_contains_only_price_classes_also_found_in_another_prices_object()
    {
        // Base PriceClasses to compare against.
        $base = (new Prices())
            ->withPricing(
                new PriceClass('A'),
                new Real(20)
            )
            ->withPricing(
                new PriceClass('B'),
                new Real(10)
            );

        // Contains the same PriceClass 'A', but with a different price.
        $some = (new Prices())
            ->withPricing(
                new PriceClass('A'),
                new Real(7.5)
            );
        $this->assertTrue($some->containsOnlyPriceClassesOf($base));

        // Contains exactly the same PriceClasses.
        $all = clone $base;
        $this->assertTrue($all->containsOnlyPriceClassesOf($base));

        // Contains another PriceClass.
        $different = (new Prices())
            ->withPricing(
                new PriceClass('C'),
                new Real(5)
            );
        $this->assertFalse($different->containsOnlyPriceClassesOf($base));
    }
}
