<?php

namespace CultuurNet\UiTPASBeheer\ResultSet;

/**
 * @method \stdClass[] getResults
 */
class MockPagedResultSet extends AbstractPagedResultSet
{
    /**
     * @var string
     */
    protected $resultClass = \stdClass::class;
}
