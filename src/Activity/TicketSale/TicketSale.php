<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Coupon\Coupon;
use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

final class TicketSale implements \JsonSerializable
{
    use TicketSaleTrait {
        TicketSaleTrait::jsonSerialize as jsonSerializeCommonProperties;
    }

    /**
     * @var StringLiteral
     */
    private $eventTitle;

    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * @param StringLiteral $id
     * @param Real $price
     * @param DateTime $creationDate
     * @param StringLiteral $eventTitle
     */
    public function __construct(
        StringLiteral $id,
        Real $price,
        DateTime $creationDate,
        StringLiteral $eventTitle
    ) {
        $this->id = $id;
        $this->price = $price;
        $this->creationDate = $creationDate;
        $this->eventTitle = $eventTitle;
    }

    /**
     * @return StringLiteral
     */
    public function getEventTitle()
    {
        return $this->eventTitle;
    }

    /**
     * @param Coupon $coupon
     * @return TicketSale
     */
    public function withCoupon(Coupon $coupon)
    {
        $c = clone $this;
        $c->coupon = $coupon;
        return $c;
    }

    /**
     * @return Coupon|null
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = $this->jsonSerializeCommonProperties();
        $data['eventTitle'] = $this->eventTitle->toNative();

        if (!is_null($this->coupon)) {
            $data['coupon'] = $this->coupon->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_TicketSale $cfTicketSale
     * @return TicketSale
     */
    public static function fromCultureFeedTicketSale(\CultureFeed_Uitpas_Event_TicketSale $cfTicketSale)
    {
        $ticketSale = new TicketSale(
            new StringLiteral((string) $cfTicketSale->id),
            new Real((float) $cfTicketSale->tariff),
            DateTime::fromNativeDateTime(new \DateTime('@' . $cfTicketSale->creationDate)),
            new StringLiteral((string) $cfTicketSale->nodeTitle)
        );

        if (!empty($cfTicketSale->ticketSaleCoupon)) {
            $ticketSale = $ticketSale->withCoupon(
                Coupon::fromCultureFeedCoupon($cfTicketSale->ticketSaleCoupon)
            );
        }

        return $ticketSale;
    }
}
