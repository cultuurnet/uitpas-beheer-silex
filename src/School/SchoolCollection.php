<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use CultureFeed_ResultSet;
use CultureFeed_Uitpas_Counter;
use TwoDotsTwice\Collection\AbstractCollection;

final class SchoolCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function getValidObjectType()
    {
        return School::class;
    }

    /**
     * @param CultureFeed_Uitpas_Counter[]
     * @return SchoolCollection
     */
    public static function fromCultureFeedCounters(
        $cfCounters
    ) {
        $schools = new self();

        foreach ($cfCounters as $cfCounter) {
            $schools = $schools->with(
                School::fromCultureFeedCounter($cfCounter)
            );
        }

        return $schools;
    }
}
