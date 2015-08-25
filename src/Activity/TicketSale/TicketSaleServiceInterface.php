<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

interface TicketSaleServiceInterface
{
    public function register(UiTPASNumber $uitpasNumber, Registration $registration);
}
