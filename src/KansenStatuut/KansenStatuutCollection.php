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
        $cfCardSystemSpecificArrayWithKansenStatuut = self::cardSystemSpecificArrayWithKansenStatuut(
            $cfCardSystemSpecificArray
        );

        $kansenStatuutCollection = new KansenStatuutCollection();

        foreach ($cfCardSystemSpecificArrayWithKansenStatuut as $cardSystemId => $cardSystemSpecific) {
            $kansenStatuut = KansenStatuut::fromCultureFeedCardSystemSpecific($cardSystemSpecific);

            $kansenStatuutCollection = $kansenStatuutCollection->withKey(
                $cardSystemSpecific->cardSystem->id,
                $kansenStatuut
            );
        }

        return $kansenStatuutCollection;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_CardSystemSpecific[] $cfCardSystemSpecificArray
     * @return \CultureFeed_Uitpas_Passholder_CardSystemSpecific[]
     */
    private static function cardSystemSpecificArrayWithKansenStatuut(array $cfCardSystemSpecificArray)
    {
        return array_filter(
            $cfCardSystemSpecificArray,
            function (
                \CultureFeed_Uitpas_Passholder_CardSystemSpecific $cardSystemSpecific
            ) {
                return $cardSystemSpecific->kansenStatuut;
            }
        );
    }
}
