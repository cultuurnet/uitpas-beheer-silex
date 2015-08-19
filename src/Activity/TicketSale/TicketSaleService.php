<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class TicketSaleService extends CounterAwareUitpasService implements TicketSaleServiceInterface
{
    public function register(UiTPASNumber $uitpasNumber, Registration $registration)
    {
        $tariffId = !is_null($registration->getTariffId()) ? $registration->getTariffId()->toNative() : null;

        $this->getUitpasService()->registerTicketSale(
            $uitpasNumber->toNative(),
            $registration->getActivityId()->toNative(),
            $registration->getAmount()->toNative(),
            $tariffId,
            $this->getCounterConsumerKey()
        );
    }
}
