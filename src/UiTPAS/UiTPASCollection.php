<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use TwoDotsTwice\Collection\AbstractCollection;

/**
 * @method UiTPASCollection with($uitpas)
 */
class UiTPASCollection extends AbstractCollection implements \JsonSerializable
{
    /**
     * @return string
     */
    protected function getValidObjectType()
    {
        return UiTPAS::class;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_CardSystemSpecific[] $cfCardSystemSpecificArray
     * @return UiTPASCollection
     */
    public static function fromCultureFeedPassholderCardSystemSpecific(array $cfCardSystemSpecificArray)
    {
        $uitpasArray = array_filter(
            array_map(
                function (\CultureFeed_Uitpas_Passholder_CardSystemSpecific $cfCardSystemSpecific) {
                    // Inconsistent data coming from API, current card is not always set!
                    if (!is_null($cfCardSystemSpecific->currentCard)) {
                        $number = new UiTPASNumber($cfCardSystemSpecific->currentCard->uitpasNumber);
                        $status = UiTPASStatus::fromNative($cfCardSystemSpecific->currentCard->status);
                        $type = UiTPASType::fromNative($cfCardSystemSpecific->currentCard->type);
                        $cardSystem = CardSystem::fromCultureFeedCardSystem($cfCardSystemSpecific->cardSystem);

                        return new UiTPAS($number, $status, $type, $cardSystem);
                    } else {
                        return null;
                    }
                },
                $cfCardSystemSpecificArray
            )
        );

        return UiTPASCollection::fromArray($uitpasArray);
    }
}
