<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

final class TicketSale implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var Real
     */
    protected $price;

    /**
     * @var DateTime
     */
    protected $creationDate;

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
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Real
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return DateTime
     */
    public function getCreationDate()
    {
       return $this->creationDate;
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
     * @return TicketSale
     */
    public static function fromCultureFeedTicketSale(\CultureFeed_Uitpas_Event_TicketSale $ticketSale)
    {
        return new TicketSale(
            new StringLiteral((string) $ticketSale->id),
            new Real((float) $ticketSale->price),
            DateTime::fromNativeDateTime(new \DateTime('@' . $ticketSale->creationDate))
        );
    }
}
