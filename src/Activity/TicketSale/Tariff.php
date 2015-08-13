<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\StringLiteral\StringLiteral;

final class Tariff implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $name;

    /**
     * @var TariffType
     */
    protected $type;

    /**
     * @var bool
     */
    protected $maximumReached;

    /**
     * @var Prices
     */
    protected $prices;

    /**
     * @param StringLiteral $name
     * @param TariffType $type
     * @param Prices $prices
     * @param bool $maximumReached
     */
    public function __construct(
        StringLiteral $name,
        TariffType $type,
        Prices $prices,
        $maximumReached = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->prices = $prices;
        $this->maximumReached = (bool) $maximumReached;
    }

    /**
     * @return Prices
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param bool $reached
     * @return Tariff
     */
    public function withMaximumReached($reached)
    {
        $c = clone $this;
        $c->maximumReached = (bool) $reached;
        return $c;
    }

    /**
     * @return bool
     */
    public function isMaximumReached()
    {
        return $this->maximumReached;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'name' => $this->name->toNative(),
            'type' => $this->type->toNative(),
            'maximumReached' => $this->maximumReached,
            'prices' => $this->prices->jsonSerialize(),
        );
    }
}
