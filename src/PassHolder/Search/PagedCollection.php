<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\Hydra\PagedCollection as HydraPagedCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;

class PagedCollection extends HydraPagedCollection
{
    /**
     * @var UiTPASNumberCollection
     */
    private $invalidUitpasNumbers;

    /**
     * @param UiTPASNumberCollection $invalidUitpasNumbers
     * @return static
     */
    public function withInvalidUitpasNumbers(UiTPASNumberCollection $invalidUitpasNumbers)
    {
        $c = clone $this;
        $c->invalidUitpasNumbers = $invalidUitpasNumbers;
        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        if (!is_null($this->invalidUitpasNumbers) &&
            $this->invalidUitpasNumbers->length() > 0) {
            $data['invalidUitpasNumbers'] = array_map(
                function (UiTPASNumber $uitpasNumber) {
                    return $uitpasNumber->toNative();
                },
                $this->invalidUitpasNumbers->toArray()
            );
        }

        return $data;
    }
}
