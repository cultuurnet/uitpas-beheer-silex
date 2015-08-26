<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Price;

use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\DateTime;
use ValueObjects\StringLiteral\StringLiteral;

final class Inquiry
{
    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    /**
     * @var PurchaseReason
     */
    protected $reason;

    /**
     * @var DateTime|null
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
     * @param UiTPASNumber $uitpasNumber
     * @param PurchaseReason $reason
     */
    public function __construct(
        UiTPASNumber $uitpasNumber,
        PurchaseReason $reason
    ) {
        $this->uitpasNumber = $uitpasNumber;
        $this->reason = $reason;
    }

    /**
     * @return UiTPASNumber
     */
    public function getUiTPASNumber()
    {
        return $this->uitpasNumber;
    }

    /**
     * @return PurchaseReason
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param DateTime $dateOfBirth
     * @return Inquiry
     */
    public function withDateOfBirth(DateTime $dateOfBirth)
    {
        $c = clone $this;
        $c->dateOfBirth = $dateOfBirth;
        return $c;
    }

    /**
     * @return DateTime|null
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
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
     * @return StringLiteral|null
     */
    public function getPostalCode()
    {
        return $this->postalCode;
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
     * @return VoucherNumber|null
     */
    public function getVoucherNumber()
    {
        return $this->voucherNumber;
    }
}
