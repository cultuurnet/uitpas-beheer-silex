<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

interface PointsTransactionServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Date $startDate
     * @param Date $endDate
     * @return PointsTransaction|null
     */
    public function search(UiTPASNumber $uitpasNumber, Date $startDate, Date $endDate);
}
