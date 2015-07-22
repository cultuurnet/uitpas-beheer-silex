<?php

namespace CultuurNet\UiTPASBeheer\Activity;

interface ActivityServiceInterface
{
    /**
     * @param mixed $query
     *
     * @return PagedResultSet
     */
    public function search($query);
}
