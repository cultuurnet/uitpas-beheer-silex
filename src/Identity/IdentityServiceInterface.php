<?php

namespace CultuurNet\UiTPASBeheer\Identity;

interface IdentityServiceInterface
{
    /**
     * @param $identification
     * @return Identity
     */
    public function get($identification);
}
