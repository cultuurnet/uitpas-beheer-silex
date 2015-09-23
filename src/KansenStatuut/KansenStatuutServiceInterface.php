<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;

interface KansenStatuutServiceInterface
{
    /**
     * @param UiTPASNumber $uiTPASNumber
     * @param CardSystemId $cardSystemId
     * @param Date $newEndDate
     * @throws ReadableCodeResponseException
     * @return void
     */
    public function renew(
        UiTPASNumber $uiTPASNumber,
        CardSystemId $cardSystemId,
        Date $newEndDate
    );
}
