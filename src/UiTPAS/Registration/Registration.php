<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Registration;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Uid;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;

final class Registration
{
    /**
     * @var Uid
     */
    private $passHolderUid;

    /**
     * @var PurchaseReason
     */
    private $reason;

    /**
     * @var KansenStatuut
     */
    private $kansenStatuut;

    /**
     * @var VoucherNumber
     */
    private $voucherNumber;

    /**
     * @param Uid $passHolderUid
     * @param PurchaseReason $reason
     */
    public function __construct(
        Uid $passHolderUid,
        PurchaseReason $reason
    ) {
        $this->passHolderUid = $passHolderUid;
        $this->reason = $reason;
    }

    /**
     * @return Uid
     */
    public function getPassHolderUid()
    {
        return $this->passHolderUid;
    }

    /**
     * @return PurchaseReason
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param KansenStatuut $kansenStatuut
     * @return Registration
     */
    public function withKansenStatuut(KansenStatuut $kansenStatuut)
    {
        $c = clone $this;
        $c->kansenStatuut = $kansenStatuut;
        return $c;
    }

    /**
     * @return KansenStatuut
     */
    public function getKansenStatuut()
    {
        return $this->kansenStatuut;
    }

    /**
     * @param VoucherNumber $voucherNumber
     * @return Registration
     */
    public function withVoucherNumber(VoucherNumber $voucherNumber)
    {
        $c = clone $this;
        $c->voucherNumber = $voucherNumber;
        return $c;
    }

    /**
     * @return VoucherNumber
     */
    public function getVoucherNumber()
    {
        return $this->voucherNumber;
    }
}
