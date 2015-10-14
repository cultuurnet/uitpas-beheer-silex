<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use ValueObjects\Number\Integer;

interface QueryInterface
{
    /**
     * @param UiTPASNumberCollection $uitpasNumbers
     *
     * @return static
     */
    public function withUiTPASNumbers(UiTPASNumberCollection $uitpasNumbers);

    /**
     * @param \ValueObjects\Number\Integer $page
     * @param \ValueObjects\Number\Integer $limit
     *
     * @return static
     */
    public function withPagination(Integer $page, Integer $limit);
}
