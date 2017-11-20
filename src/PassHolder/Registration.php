<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\School\SchoolConsumerKey;

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
     * @var SchoolConsumerKey
     */
    protected $schoolConsumerKey;

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
     * @return SchoolConsumerKey|null
     */
    public function getSchoolConsumerKey()
    {
        return $this->schoolConsumerKey;
    }

    /**
     * @param VoucherNumber $voucherNumber
     * @return Registration
     */
    public function withVoucherNumber(VoucherNumber $voucherNumber)
    {
        return $this->with('voucherNumber', $voucherNumber);
    }

    /**
     * @param KansenStatuut $kansenStatuut
     * @return Registration
     */
    public function withKansenstatuut(KansenStatuut $kansenStatuut)
    {
        return $this->with('kansenStatuut', $kansenStatuut);
    }

    /**
     * @param SchoolConsumerKey $schoolConsumerKey
     * @return Registration
     */
    public function withSchoolConsumerKey(SchoolConsumerKey $schoolConsumerKey)
    {
        return $this->with('schoolConsumerKey', $schoolConsumerKey);
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return Registration
     */
    private function with($property, $value)
    {
        $c = clone $this;
        $c->{$property} = $value;
        return $c;
    }
}
