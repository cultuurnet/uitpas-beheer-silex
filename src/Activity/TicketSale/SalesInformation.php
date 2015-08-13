<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

class SalesInformation implements \JsonSerializable
{
    /**
     * @var Prices
     */
    protected $basePrices;

    /**
     * @var Tariff[]
     */
    protected $tariffs;

    /**
     * @var bool
     */
    protected $maximumReached;

    /**
     * @param Prices $basePrices
     */
    public function __construct(Prices $basePrices)
    {
        $this->basePrices = $basePrices;
        $this->tariffs = array();
        $this->maximumReached = true;
    }

    /**
     * @return bool
     */
    public function isMaximumReached()
    {
        return $this->maximumReached;
    }

    /**
     * @param Tariff $tariff
     * @return SalesInformation
     */
    public function withTariff(Tariff $tariff)
    {
        if (!$tariff->getPrices()->containsOnlyPriceClassesOf($this->basePrices)) {
            throw new \InvalidArgumentException(
                'The provided tariff has a price for a price class that is not in the base prices.'
            );
        }

        $c = clone $this;
        $c->tariffs[] = $tariff;

        // Set the total maximum reached to false if the new tariff has not
        // reached the maximum. Note that we can't just set $c->maximumReached
        // to $tariff->isMaximumReached() as that could set $c->maximumReached
        // back to true when a new tariff is added that has reached the
        // maximum.
        $c->maximumReached = $c->maximumReached && $tariff->isMaximumReached();

        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $tariffs = array();
        foreach ($this->tariffs as $tariff) {
            $tariffs[] = $tariff->jsonSerialize();
        }

        return array(
            'maximumReached' => $this->maximumReached,
            'base' => $this->basePrices->jsonSerialize(),
            'tariffs' => $tariffs,
        );
    }
}
