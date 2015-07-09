<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

interface ActivityServiceInterface
{
    /**
     * @param DateType $date
     * @param \ValueObjects\Number\Integer $limit
     * @param \ValueObjects\StringLiteral\StringLiteral $query
     * @param \ValueObjects\Number\Integer $page
     */
    public function search(DateType $date, Integer $limit, StringLiteral $query = null, Integer $page = null);
}
