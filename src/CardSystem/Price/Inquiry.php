<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem\Price;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

final class Inquiry
{
    /**
     * @var CardSystemId
     */
    protected $cardSystemId;

    /**
     * @var Date|null
     */
    protected $dateOfBirth;

    /**
     * @var StringLiteral|null
     */
    protected $postalCode;

    /**
     * @var VoucherNumber|null
     */
    protected $voucherNumber;

    /**
     * @param CardSystemId $cardSystemId
     */
    public function __construct(CardSystemId $cardSystemId)
    {
        $this->cardSystemId = $cardSystemId;
    }

    /**
     * @param Date $dateOfBirth
     * @return Inquiry
     */
    public function withDateOfBirth(Date $dateOfBirth)
    {
        $c = clone $this;
        $c->dateOfBirth = $dateOfBirth;
        return $c;
    }

    /**
     * @param StringLiteral $postalCode
     * @return Inquiry
     */
    public function withPostalCode(StringLiteral $postalCode)
    {
        $c = clone $this;
        $c->postalCode = $postalCode;
        return $c;
    }
    /**
     * @param VoucherNumber $voucherNumber
     * @return Inquiry
     */
    public function withVoucherNumber(VoucherNumber $voucherNumber)
    {
        $c = clone $this;
        $c->voucherNumber = $voucherNumber;
        return $c;
    }

    /**
     * @return CardSystemId
     */
    public function getCardSystemId()
    {
        return $this->cardSystemId;
    }

    /**
     * @return null|Date
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @return null|StringLiteral
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @return VoucherNumber|null
     */
    public function getVoucherNumber()
    {
        return $this->voucherNumber;
    }
}
