<?php

namespace CultuurNet\UiTPASBeheer\CardSystem;

use TwoDotsTwice\Collection\AbstractCollection;

class CardSystemCollection extends AbstractCollection implements \JsonSerializable
{
    /**
     * @return string
     */
    protected function getValidObjectType()
    {
        return CardSystem::class;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_CardSystemSpecific[] $cfCardSystemSpecificArray
     * @return CardSystemCollection
     */
    public static function fromCultureFeedPassHolderCardSystemSpecific(
        array $cfCardSystemSpecificArray
    ) {
        $cardSystemCollection = new self();

        foreach ($cfCardSystemSpecificArray as $cfCardSystemSpecific) {
            $cardSystem = CardSystem::fromCultureFeedCardSystem($cfCardSystemSpecific->cardSystem);
            $cardSystemCollection = $cardSystemCollection->with($cardSystem);
        }

        return $cardSystemCollection;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return array_values($this->toArray());
    }
}
