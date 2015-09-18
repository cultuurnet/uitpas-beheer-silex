<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use TwoDotsTwice\Collection\AbstractCollection;

final class KansenStatuutCollection extends AbstractCollection implements \JsonSerializable
{
    /**
     * @return string
     */
    protected function getValidObjectType()
    {
        return KansenStatuut::class;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
