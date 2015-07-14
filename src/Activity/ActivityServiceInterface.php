<?php

namespace CultuurNet\UiTPASBeheer\Activity;

interface ActivityServiceInterface
{
    /**
     * @param mixed $query
     *
     * @return Activity[]
     */
    public function search($query);
}
