<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\TicketSaleTrait;
use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

final class RegisteredTicketSale implements \JsonSerializable
{
    use TicketSaleTrait;

    /**
     * @param StringLiteral $id
     * @param Real $price
     * @param DateTime $creationDate
     */
    public function __construct(
        StringLiteral $id,
        Real $price,
        DateTime $creationDate
    ) {
        $this->id = $id;
        $this->price = $price;
        $this->creationDate = $creationDate;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id->toNative(),
            'price' => $this->price->toNative(),
            'creationDate' => $this->creationDate->toNativeDateTime()->format(\DateTime::RFC3339),
        ];
    }

    /**
     * @param \CultureFeed_Uitpas_Event_TicketSale $ticketSale
     * @return RegisteredTicketSale
     */
    public static function fromCultureFeedTicketSale(\CultureFeed_Uitpas_Event_TicketSale $ticketSale)
    {
        return new RegisteredTicketSale(
            new StringLiteral((string) $ticketSale->id),
            new Real((float) $ticketSale->price),
            DateTime::fromNativeDateTime(new \DateTime('@' . $ticketSale->creationDate))
        );
    }
}
