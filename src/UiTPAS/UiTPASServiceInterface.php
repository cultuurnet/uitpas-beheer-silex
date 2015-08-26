<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;

interface UiTPASServiceInterface
{
    /**
     * @param Inquiry $inquiry
     *
     * @return Price
     *
     * @throws ReadableCodeResponseException
     *   When an UiTPAS API error occurred.
     */
    public function getPrice(Inquiry $inquiry);
}
