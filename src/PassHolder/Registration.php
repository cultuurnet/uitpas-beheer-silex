<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

final class Registration
{
    /**
     * @var PassHolder
     */
    protected $passHolder;

    /**
     * @var VoucherNumber
     */
    protected $voucherNumber;

    /**
     * @var KansenStatuut
     */
    protected $kansenStatuut;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    /**
     * Registration constructor.
     * @param PassHolder $passHolder
     */
    public function __construct(PassHolder $passHolder)
    {
        $this->passHolder = $passHolder;
    }

    /**
     * @return PassHolder
     */
    public function getPassHolder()
    {
        return $this->passHolder;
    }

    /**
     * @return VoucherNumber|null
     */
    public function getVoucherNumber()
    {
        return $this->voucherNumber;
    }

    /**
     * @return KansenStatuut|null
     */
    public function getKansenStatuut()
    {
        return $this->kansenStatuut;
    }

    /**
     * @param VoucherNumber $voucherNumber
     * @return PassHolder
     */
    public function withVoucherNumber(VoucherNumber $voucherNumber)
    {
        return $this->with('voucherNumber', $voucherNumber);
    }

    /**
     * @param KansenStatuut $kansenStatuut
     * @return PassHolder
     */
    public function withKansenstatuut(KansenStatuut $kansenStatuut)
    {
        return $this->with('kansenStatuut', $kansenStatuut);
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return PassHolder
     */
    private function with($property, $value)
    {
        $c = clone $this;
        $c->{$property} = $value;
        return $c;
    }
}
