<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\RegisteredTicketSale;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

interface TicketSaleServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Registration $registration
     * @return RegisteredTicketSale
     */
    public function register(UiTPASNumber $uitpasNumber, Registration $registration);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return TicketSale[]
     */
    public function getByUiTPASNumber(UiTPASNumber $uitpasNumber);
}
