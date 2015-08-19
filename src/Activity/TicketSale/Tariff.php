<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\Number\Real;
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
     * @return TariffType
     */
    public function getType()
    {
        return $this->type;
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
    public function hasReachedMaximum()
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

    /**
     * @param \CultureFeed_Uitpas_Event_TicketSale_Opportunity $ticketSale
     * @return Tariff
     *
     * @throws \InvalidArgumentException
     *   When the provided ticketSale is of an unknown type.
     */
    public static function fromCultureFeedTicketSaleOpportunity(
        \CultureFeed_Uitpas_Event_TicketSale_Opportunity $ticketSale
    ) {
        // Determine the ticketSale's type and name.
        switch ($ticketSale->type) {
            case \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_DEFAULT:
                $tariffName = new StringLiteral('Kansentarief');
                $tariffType = TariffType::KANSENTARIEF();
                break;

            case \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_COUPON:
                $tariffName = new StringLiteral($ticketSale->ticketSaleCoupon->name);
                $tariffType = TariffType::COUPON();
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'Provided $ticketSale argument is of an unknown type "%s".',
                        $ticketSale->type
                    )
                );
                break;
        }

        // Determine this tariff's prices.
        $tariffPrices = new Prices();
        foreach ($ticketSale->priceClasses as $priceClass) {
            $tariffPrices = $tariffPrices->withPricing(
                new PriceClass($priceClass->name),
                new Real($priceClass->tariff)
            );
        }

        // Determine whether this ticketSale has reached its maximum number
        // of sales.
        $maximumReached = $ticketSale->buyConstraintReason ===
            \CultureFeed_Uitpas_Event_TicketSale_Opportunity::BUY_CONSTRAINT_MAXIMUM_REACHED;

        return new Tariff(
            $tariffName,
            $tariffType,
            $tariffPrices,
            $maximumReached
        );
    }
}
