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

    /**
     * @param \CultureFeed_Uitpas_Passholder $passHolder
     * @return CardSystemCollection
     */
    public static function fromCultureFeedPassHolder(
        \CultureFeed_Uitpas_Passholder $passHolder
    ) {
        $cardSystemCollection = new self();

        foreach ($passHolder->cardSystemSpecific as $cardSystemSpecific) {
            $cardSystem = CardSystem::fromCultureFeedCardSystem($cardSystemSpecific->cardSystem);
            $cardSystemCollection = $cardSystemCollection->with($cardSystem);
        }

        return $cardSystemCollection;
    }
}
