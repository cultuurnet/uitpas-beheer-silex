<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use TwoDotsTwice\Collection\AbstractCollection;

/**
 * @method UiTPASNumberCollection with($item)
 */
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
