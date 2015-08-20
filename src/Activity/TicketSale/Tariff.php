<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

/**
 * Class Tariff
 * @package CultuurNet\UiTPASBeheer\Activity\TicketSale
 *
 * @todo Refactor so this becomes an abstract class and we have two
 * other classes eg. KansentariefTariff and CouponTariff?
 */
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
     * @var StringLiteral
     */
    protected $id;

    /**
     * @param StringLiteral $name
     * @param TariffType $type
     * @param Prices $prices
     * @param StringLiteral $id
     */
    public function __construct(
        StringLiteral $name,
        TariffType $type,
        Prices $prices,
        StringLiteral $id = null
    ) {
        if (is_null($id) && !$type->is(TariffType::KANSENTARIEF())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Required argument id missing for type "%s".',
                    $type->toNative()
                )
            );
        } elseif (!is_null($id) && $type->is(TariffType::KANSENTARIEF())) {
            throw new \InvalidArgumentException(
                'Argument id should be null for type "KANSENTARIEF".'
            );
        }

        $this->name = $name;
        $this->type = $type;
        $this->prices = $prices;
        $this->id = $id;
        $this->maximumReached = false;
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
     * @return StringLiteral|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param bool $reached
     * @return Tariff
     */
    public function withMaximumReached($reached = true)
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
        $data = array(
            'name' => $this->name->toNative(),
            'type' => $this->type->toNative(),
            'maximumReached' => $this->maximumReached,
            'prices' => $this->prices->jsonSerialize(),
        );

        if (!is_null($this->id)) {
            $data['id'] = $this->id->toNative();
        }

        return $data;
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
                $name = new StringLiteral('Kansentarief');
                $type = TariffType::KANSENTARIEF();
                $id = null;
                break;

            case \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_COUPON:
                $name = new StringLiteral($ticketSale->ticketSaleCoupon->name);
                $type = TariffType::COUPON();
                $id = new StringLiteral($ticketSale->ticketSaleCoupon->id);
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
        $prices = new Prices();
        foreach ($ticketSale->priceClasses as $priceClass) {
            $prices = $prices->withPricing(
                new PriceClass($priceClass->name),
                new Real($priceClass->tariff)
            );
        }

        // Determine whether this ticketSale has reached its maximum number
        // of sales.
        $maximumReached = $ticketSale->buyConstraintReason ===
            \CultureFeed_Uitpas_Event_TicketSale_Opportunity::BUY_CONSTRAINT_MAXIMUM_REACHED;

        return (new Tariff(
            $name,
            $type,
            $prices,
            $id
        ))->withMaximumReached($maximumReached);
    }
}
