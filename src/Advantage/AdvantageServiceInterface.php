<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

interface AdvantageServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     * @return Advantage|null
     */
    public function get(UiTPASNumber $uitpasNumber, StringLiteral $id);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param int $max
     * @param int $start
     * @return Advantage[]
     */
    public function getExchangeable(UiTPASNumber $uitpasNumber, $max = null, $start = null);

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param StringLiteral $id
     */
    public function exchange(UiTPASNumber $uitpasNumber, StringLiteral $id);
}
