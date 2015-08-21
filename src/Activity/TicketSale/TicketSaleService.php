<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class TicketSaleService extends CounterAwareUitpasService implements TicketSaleServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Registration $registration
     *
     * @return TicketSale
     *
     * @throws ReadableCodeResponseException
     *   When a CultureFeed_Exception was caught.
     */
    public function register(UiTPASNumber $uitpasNumber, Registration $registration)
    {
        $tariffId = $registration->getTariffId();

        if (!is_null($tariffId)) {
            $tariffId = $tariffId->toNative();
        }

        try {
            $cfTicketSale = $this->getUitpasService()->registerTicketSale(
                $uitpasNumber->toNative(),
                $registration->getActivityId()->toNative(),
                $this->getCounterConsumerKey(),
                $registration->getPriceClass()->toNative(),
                $tariffId
            );
        } catch (\CultureFeed_Exception $e) {
            throw ReadableCodeResponseException::fromCultureFeedException($e);
        }

        return TicketSale::fromCultureFeedTicketSale($cfTicketSale);
    }
}
