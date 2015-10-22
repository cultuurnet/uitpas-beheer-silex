<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\Identity\Identity;
use CultuurNet\UiTPASBeheer\ResultSet\AbstractPagedResultSet;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;

/**
 * @method Identity[] getResults()
 */
class PagedResultSet extends AbstractPagedResultSet
{
    /**
     * @var string
     */
    protected $resultClass = Identity::class;

    /**
     * @var UiTPASNumberCollection|null
     */
    protected $invalidUitpasNumbers;

    /**
     * @param UiTPASNumberCollection $invalidUitpasNumbers
     * @return PagedResultSet
     */
    public function withInvalidUiTPASNumbers(UiTPASNumberCollection $invalidUitpasNumbers)
    {
        $c = clone $this;
        $c->invalidUitpasNumbers = $invalidUitpasNumbers;
        return $c;
    }

    /**
     * @return UiTPASNumberCollection|null
     */
    public function getInvalidUitpasNumbers()
    {
        return $this->invalidUitpasNumbers;
    }
}
