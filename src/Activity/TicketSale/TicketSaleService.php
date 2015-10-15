<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class TicketSaleService extends CounterAwareUitpasService implements TicketSaleServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Registration $registration
     *
     * @return TicketSale
     *
     * @throws CompleteResponseException
     *   When a CultureFeed_Exception was caught.
     */
    public function register(UiTPASNumber $uitpasNumber, Registration $registration)
    {
        $tariffId = $registration->getTariffId();

        if (!is_null($tariffId)) {
            $tariffId = $tariffId->toNative();
        }

        $amount = $registration->getAmount();
        if ($amount) {
            $amount = $amount->toNative();
        }

        try {
            $cfTicketSale = $this->getUitpasService()->registerTicketSale(
                $uitpasNumber->toNative(),
                $registration->getActivityId()->toNative(),
                $this->getCounterConsumerKey(),
                $registration->getPriceClass()->toNative(),
                $tariffId,
                $amount
            );
        } catch (\CultureFeed_Exception $e) {
            throw CompleteResponseException::fromCultureFeedException($e);
        }

        return TicketSale::fromCultureFeedTicketSale($cfTicketSale);
    }
}
