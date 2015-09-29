<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;

interface UiTPASServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @return UiTPAS
     */
    public function block(UiTPASNumber $uitpasNumber);

    /**
     * @param Inquiry $inquiry
     * @return Price
     */
    public function getPrice(Inquiry $inquiry);
}
