<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

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
     * @return array
     */
    public function jsonSerialize()
    {
        $data = $this->jsonSerializeCommonProperties();
        $data['eventTitle'] = $this->eventTitle->toNative();
        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_TicketSale $ticketSale
     * @return TicketSale
     */
    public static function fromCultureFeedTicketSale(\CultureFeed_Uitpas_Event_TicketSale $ticketSale)
    {
        return new TicketSale(
            new StringLiteral((string) $ticketSale->id),
            new Real((float) $ticketSale->tariff),
            DateTime::fromNativeDateTime(new \DateTime('@' . $ticketSale->creationDate)),
            new StringLiteral($ticketSale->nodeTitle)
        );
    }
}
