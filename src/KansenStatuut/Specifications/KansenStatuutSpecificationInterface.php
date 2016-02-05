<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut\Specifications;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;

interface KansenStatuutSpecificationInterface
{
    /**
     * @param KansenStatuut $kansenStatuut
     * @return bool
     */
    public function isSatisfiedBy(KansenStatuut $kansenStatuut);
}
