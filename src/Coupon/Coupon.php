<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use ValueObjects\StringLiteral\StringLiteral;

class Coupon implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $name;

    public function __construct(StringLiteral $id, stringLiteral $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return StringLiteral
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
        ];
    }

    /**
     * @param \CultureFeed_Uitpas_Event_TicketSale_Coupon $cfCoupon
     * @return Coupon
     */
    public static function fromCultureFeedCoupon(\CultureFeed_Uitpas_Event_TicketSale_Coupon $cfCoupon)
    {
        $coupon = new Coupon(
            new StringLiteral($cfCoupon->id),
            new StringLiteral($cfCoupon->name)
        );

        return $coupon;
    }
}
