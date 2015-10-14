<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use TwoDotsTwice\Collection\AbstractCollection;

class UiTPASNumberCollection extends AbstractCollection
{
    /**
     * @return string
     */
    protected function getValidObjectType()
    {
        return UiTPASNumber::class;
    }
}
