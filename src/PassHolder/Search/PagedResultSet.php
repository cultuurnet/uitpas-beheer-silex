<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\PassHolder\PassHolder;
use CultuurNet\UiTPASBeheer\ResultSet\AbstractPagedResultSet;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;

/**
 * @method PassHolder[] getResults()
 */
class PagedResultSet extends AbstractPagedResultSet
{
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
        $c->$invalidUitpasNumbers = $invalidUitpasNumbers;
        return $c;
    }

    /**
     * @return UiTPASNumberCollection|null
     */
    public function getInvalidUitpasNumbers()
    {
        return $this->invalidUitpasNumbers;
    }

    /**
     * @var string
     */
    protected $resultClass = PassHolder::class;
}
