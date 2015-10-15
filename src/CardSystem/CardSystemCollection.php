<?php

namespace CultuurNet\UiTPASBeheer\CardSystem;

use TwoDotsTwice\Collection\AbstractCollection;

class CardSystemCollection extends AbstractCollection
{
    /**
     * @return string
     */
    protected function getValidObjectType()
    {
        return CardSystem::class;
    }
}
