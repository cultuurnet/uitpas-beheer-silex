<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class Registration
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
     * Registration constructor.
     * @param PassHolder $passholder
     * @param VoucherNumber|null $voucherNumber
     */
    public function __construct(
        PassHolder $passholder,
        VoucherNumber $voucherNumber = null
    ) {
        $this->passholder = $passholder;
        $this->voucherNumber = $voucherNumber;
    }

    /**
     * @return PassHolder
     */
    public function getPassholder()
    {
        return $this->passholder;
    }

    /**
     * @return VoucherNumber
     */
    public function getVoucherNumber()
    {
        return $this->voucherNumber;
    }
}
