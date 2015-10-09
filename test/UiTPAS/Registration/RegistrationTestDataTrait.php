<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Registration;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\Properties\Uid;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;

trait RegistrationTestDataTrait
{
    /**
     * @return Uid
     */
    public function getPassHolderUid()
    {
        return new Uid('5');
    }

    /**
     * @return PurchaseReason
     */
    public function getReason()
    {
        return PurchaseReason::LOSS_THEFT();
    }

    /**
     * @return KansenStatuut
     */
    public function getKansenStatuut()
    {
        return new KansenStatuut(
            new Date(
                new Year(2015),
                Month::getByName('DECEMBER'),
                new MonthDay(31)
            )
        );
    }

    /**
     * @return VoucherNumber
     */
    public function getVoucherNumber()
    {
        return new VoucherNumber('abc-123');
    }

    /**
     * @return Registration
     */
    public function getMinimalRegistration()
    {
        return new Registration(
            $this->getPassHolderUid(),
            $this->getReason()
        );
    }

    /**
     * @return Registration
     */
    public function getCompleteRegistration()
    {
        return $this->getMinimalRegistration()
            ->withKansenStatuut(
                $this->getKansenStatuut()
            )
            ->withVoucherNumber(
                $this->getVoucherNumber()
            );
    }
}
