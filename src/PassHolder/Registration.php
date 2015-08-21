<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\Kansenstatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

final class Registration
{
    /**
     * @var PassHolder
     */
    protected $passholder;

    /**
     * @var VoucherNumber
     */
    protected $voucherNumber;

    /**
     * @var Kansenstatuut
     */
    protected $kansenstatuut;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    /**
     * Registration constructor.
     * @param PassHolder $passholder
     */
    public function __construct(PassHolder $passholder)
    {
        $this->passholder = $passholder;
    }

    /**
     * @return PassHolder
     */
    public function getPassholder()
    {
        return $this->passholder;
    }

    /**
     * @return VoucherNumber|null
     */
    public function getVoucherNumber()
    {
        return $this->voucherNumber;
    }

    /**
     * @return Kansenstatuut|null
     */
    public function getKansenstatuut()
    {
        return $this->kansenstatuut;
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
     * @param Kansenstatuut $kansenstatuut
     * @return PassHolder
     */
    public function withKansenstatuut(Kansenstatuut $kansenstatuut)
    {
        return $this->with('kansenstatuut', $kansenstatuut);
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
