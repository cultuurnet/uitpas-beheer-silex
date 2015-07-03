<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

interface AdvantageServiceInterface
{
    /**
     * @return AdvantageType
     */
    public function getType();

    /**
     * @return Advantage|null
     */
    public function get(UiTPASNumber $uitpasNumber, StringLiteral $id);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return Advantage[]
     */
    public function getExchangeable(UiTPASNumber $uitpasNumber);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     */
    public function exchange(UiTPASNumber $uitpasNumber, StringLiteral $id);
}
