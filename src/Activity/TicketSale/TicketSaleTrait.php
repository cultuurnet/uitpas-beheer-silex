<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

/**
 * Contains all common properties shared among the various RegisteredTicketSale models.
 */
trait TicketSaleTrait
{
    /**
     * @var StringLiteral
     */
    private $id;

    /**
     * @var Real
     */
    private $price;

    /**
     * @var DateTime
     */
    private $creationDate;

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
}
