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

    /**
     * @param \CultureFeed_Uitpas_Passholder_CardSystemSpecific[] $cfCardSystemSpecificArray
     * @return KansenStatuutCollection
     */
    public static function fromCultureFeedPassholderCardSystemSpecific(array $cfCardSystemSpecificArray)
    {
        $kansenStatuutCollection = new KansenStatuutCollection();

        foreach ($cfCardSystemSpecificArray as $cardSystemId => $cardSystemSpecific) {
            try {
                $kansenStatuut = KansenStatuut::fromCultureFeedCardSystemSpecific($cardSystemSpecific);

                $kansenStatuutCollection = $kansenStatuutCollection->withKey(
                    $cardSystemSpecific->cardSystem->id,
                    $kansenStatuut
                );
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        return $kansenStatuutCollection;
    }
}
