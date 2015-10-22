<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\Registration;

interface UiTPASServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     */
    public function block(UiTPASNumber $uitpasNumber);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return UiTPAS
     */
    public function get(UiTPASNumber $uitpasNumber);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Registration $registration
     */
    public function register(UiTPASNumber $uitpasNumber, Registration $registration);

    /**
     * @param Inquiry $inquiry
     * @return Price
     */
    public function getPrice(Inquiry $inquiry);
}
