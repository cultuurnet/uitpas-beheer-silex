<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\Number\Real;

final class Prices implements \JsonSerializable
{
    /**
     * @var Real[]
     */
    protected $prices;

    public function __construct()
    {
        $this->prices = array();
    }

    /**
     * @param PriceClass $priceClass
     * @param Real $price
     *
     * @return Prices
     */
    public function withPricing(PriceClass $priceClass, Real $price)
    {
        $c = clone $this;
        $c->prices[(string) $priceClass] = $price;
        return $c;
    }

    /**
     * Makes sure that this object only has prices for price classes that are
     * also present in the provided Prices object.
     *
     * Does NOT check that it has exactly the same price classes as the
     * provided object.
     *
     * @param Prices $prices
     * @return bool
     */
    public function containsOnlyPriceClassesOf(Prices $prices)
    {
        $expectedPriceClasses = array_keys($prices->prices);
        $actualPriceClasses = array_keys($this->prices);

        foreach ($actualPriceClasses as $actualPriceClass) {
            if (!in_array($actualPriceClass, $expectedPriceClasses)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $prices = array();
        foreach ($this->prices as $key => $price) {
            $prices[$key] = $price->toNative();
        }
        return $prices;
    }
}
