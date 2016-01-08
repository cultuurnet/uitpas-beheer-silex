<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut\Filter;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutCollection;

interface KansenStatuutFilterInterface
{
    /**
     * @param KansenStatuutCollection $kansenStatuutCollection
     * @return KansenStatuutCollection
     */
    public function filter(KansenStatuutCollection $kansenStatuutCollection);
}
