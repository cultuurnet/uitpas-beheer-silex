<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\ResultSet\AbstractPagedResultSet;

/**
 * @method Activity[] getResults
 */
class PagedResultSet extends AbstractPagedResultSet
{
    /**
     * @var string
     */
    protected $resultClass = Activity::class;
}
