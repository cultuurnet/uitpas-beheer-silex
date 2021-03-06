<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use ValueObjects\StringLiteral\StringLiteral;

class CardSystemCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_return_a_collection_from_culturefeed_uitpas_passholder_card_system_specific()
    {
        $cardSystem1Info = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystem1Info->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            1,
            'UiTPAS regio Aalst'
        );

        $cardSystem20Info = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystem20Info->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            20,
            'UiTPAS regio Brussel'
        );

        $cardSystemSpecific = [
            $cardSystem1Info,
            $cardSystem20Info,
        ];

        $collection = CardSystemCollection::fromCultureFeedPassHolderCardSystemSpecific($cardSystemSpecific);

        $expectedCollection = (new CardSystemCollection())
            ->with(
                new CardSystem(
                    new CardSystemId('1'),
                    new StringLiteral('UiTPAS regio Aalst')
                )
            )
            ->with(
                new CardSystem(
                    new CardSystemId('20'),
                    new StringLiteral('UiTPAS regio Brussel')
                )
            );

        $this->assertEquals(
            $expectedCollection,
            $collection
        );
    }
}
