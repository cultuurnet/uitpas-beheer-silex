<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

class UiTPASCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_omits_missing_cards_when_initializing_from_a_culturefeed_passholder()
    {
         // Card system specific A: Has a regular card.
        $cardSystemSpecificA = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();

        $cardSystemA = new \CultureFeed_Uitpas_CardSystem();
        $cardSystemA->id = 1;
        $cardSystemA->name = 'CardSystem A';

        $cardA = new \CultureFeed_Uitpas_Passholder_Card();
        $cardA->uitpasNumber = '0930000420206';
        $cardA->status = 'ACTIVE';
        $cardA->type = 'CARD';
        $cardA->kansenpas = false;
        $cardA->cardSystem = $cardSystemA;

        $cardSystemSpecificA->cardSystem = $cardSystemA;
        $cardSystemSpecificA->currentCard = $cardA;

        // Card system specific B: Has no card.
        $cardSystemSpecificB = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();

        $cardSystemB = new \CultureFeed_Uitpas_CardSystem();
        $cardSystemB->id = 2;
        $cardSystemB->name = 'CardSystem B';

        $cardSystemSpecificB->cardSystem = $cardSystemB;

        // Card system specific C: Has a blocked kansenstatuut key.
        $cardSystemSpecificC = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();

        $cardSystemC = new \CultureFeed_Uitpas_CardSystem();
        $cardSystemC->id = 3;
        $cardSystemC->name = 'CardSystem C';

        $cardC = new \CultureFeed_Uitpas_Passholder_Card();
        $cardC->uitpasNumber = '0930000237915';
        $cardC->status = 'BLOCKED';
        $cardC->type = 'KEY';
        $cardC->kansenpas = false;
        $cardC->cardSystem = $cardSystemC;

        $cardSystemSpecificC->cardSystem = $cardSystemC;
        $cardSystemSpecificC->currentCard = $cardC;

        // All card system specific data in one array.
        $cardSystemSpecific = array(
            $cardSystemSpecificA,
            $cardSystemSpecificB,
            $cardSystemSpecificC,
        );

        // Expected UiTPASCollection
        $uitpasA = UiTPAS::fromCultureFeedPassHolderCard($cardA);
        $uitpasC = UiTPAS::fromCultureFeedPassHolderCard($cardC);

        $uitpasCollection = (new UiTPASCollection())
            ->with($uitpasA)
            ->with($uitpasC);

        $this->assertEquals(
            $uitpasCollection,
            UiTPASCollection::fromCultureFeedPassholderCardSystemSpecific($cardSystemSpecific)
        );
    }
}
