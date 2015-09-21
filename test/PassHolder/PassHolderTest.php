<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutCollection;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceEmail;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferences;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceSMS;

class PassHolderTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var \CultureFeed_Uitpas_Passholder
     */
    protected $cfPassHolderMinimal;

    /**
     * @var \CultureFeed_Uitpas_Passholder
     */
    protected $cfPassHolderFull;

    public function setUp()
    {
        $this->cfPassHolderMinimal = new \CultureFeed_Uitpas_Passholder();
        $this->cfPassHolderMinimal->name = 'Zyrani';
        $this->cfPassHolderMinimal->firstName = 'Layla';
        $this->cfPassHolderMinimal->postalCode = '1090';
        $this->cfPassHolderMinimal->city = 'Jette (Brussel)';
        $this->cfPassHolderMinimal->dateOfBirth = 211420800;

        $this->cfPassHolderFull = clone $this->cfPassHolderMinimal;
        $this->cfPassHolderFull->street = 'Rue Perdue 101 /0003';
        $this->cfPassHolderFull->placeOfBirth = 'Casablanca';
        $this->cfPassHolderFull->secondName = 'Zoni';
        $this->cfPassHolderFull->gender = 'FEMALE';
        $this->cfPassHolderFull->inszNumber = '93051822361';
        $this->cfPassHolderFull->nationality = 'Maroc';
        $this->cfPassHolderFull->picture = 'R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=';
        $this->cfPassHolderFull->points = 20;
        $this->cfPassHolderFull->email = 'zyrani_.hotmail.com@mailinator.com';
        $this->cfPassHolderFull->telephone = '0488694231';
        $this->cfPassHolderFull->gsm = '0499748596';
        $this->cfPassHolderFull->smsPreference = 'NO_SMS';
        $this->cfPassHolderFull->emailPreference = 'ALL_MAILS';

        $this->cfPassHolderFull->cardSystemSpecific[10] = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $this->cfPassHolderFull->cardSystemSpecific[10]->kansenStatuut = true;
        $this->cfPassHolderFull->cardSystemSpecific[10]->kansenStatuutEndDate = 1442331412;
        $this->cfPassHolderFull->cardSystemSpecific[10]->kansenStatuutExpired = false;
        $this->cfPassHolderFull->cardSystemSpecific[10]->kansenStatuutInGracePeriod = true;
        $this->cfPassHolderFull->cardSystemSpecific[10]->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            10,
            'UiTPAS Regio Aalst'
        );

        $this->cfPassHolderFull->cardSystemSpecific[20] = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $this->cfPassHolderFull->cardSystemSpecific[20]->kansenStatuut = false;
        $this->cfPassHolderFull->cardSystemSpecific[20]->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            20,
            'UiTPAS Regio Kortrijk'
        );

        $this->cfPassHolderFull->cardSystemSpecific[30] = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $this->cfPassHolderFull->cardSystemSpecific[30]->kansenStatuut = true;
        $this->cfPassHolderFull->cardSystemSpecific[30]->kansenStatuutEndDate = 1442331412;
        $this->cfPassHolderFull->cardSystemSpecific[30]->kansenStatuutExpired = true;
        $this->cfPassHolderFull->cardSystemSpecific[30]->kansenStatuutInGracePeriod = false;
        $this->cfPassHolderFull->cardSystemSpecific[30]->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            30,
            'UiTPAS Regio Brussel'
        );
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $passHolder = PassHolder::fromCultureFeedPassHolder($this->cfPassHolderFull);

        // The following fromCultureFeedPassHolder() methods are tested in the
        // classes' respective tests.
        $expectedName = Name::fromCultureFeedPassHolder($this->cfPassHolderFull);
        $expectedAddress = Address::fromCultureFeedPassHolder($this->cfPassHolderFull);
        $expectedBirthInformation = BirthInformation::fromCultureFeedPassHolder($this->cfPassHolderFull);
        $expectedContactInformation = ContactInformation::fromCultureFeedPassHolder($this->cfPassHolderFull);
        $expectedPrivacyPreferences = PrivacyPreferences::fromCultureFeedPassHolder($this->cfPassHolderFull);

        $this->assertTrue($passHolder->getName()->sameValueAs($expectedName));
        $this->assertTrue($passHolder->getAddress()->sameValueAs($expectedAddress));
        $this->assertTrue($passHolder->getBirthInformation()->sameValueAs($expectedBirthInformation));
        $this->assertTrue($passHolder->getContactInformation()->sameValueAs($expectedContactInformation));
        $this->assertTrue($passHolder->getPrivacyPreferences()->sameValueAs($expectedPrivacyPreferences));

        $this->assertEquals(2, $passHolder->getKansenStatuten()->length());
        $this->assertEquals(
            KansenStatuut::fromCultureFeedCardSystemSpecific($this->cfPassHolderFull->cardSystemSpecific[10]),
            $passHolder->getKansenStatuten()->getByKey(10)
        );
        $this->assertEquals(
            KansenStatuut::fromCultureFeedCardSystemSpecific($this->cfPassHolderFull->cardSystemSpecific[30]),
            $passHolder->getKansenStatuten()->getByKey(30)
        );

        $this->assertEquals('93051822361', $passHolder->getINSZNumber()->toNative());
        $this->assertEquals('FEMALE', $passHolder->getGender()->toNative());
        $this->assertEquals('Maroc', $passHolder->getNationality()->toNative());
        $this->assertEquals(20, $passHolder->getPoints()->toNative());
        $this->assertEquals(
            'R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=',
            $passHolder->getPicture()->toNative()
        );
    }

    /**
     * @test
     */
    public function it_can_handle_missing_optional_properties_while_extracting_from_a_culturefeed_passholder()
    {
        $passHolder = PassHolder::fromCultureFeedPassHolder($this->cfPassHolderMinimal);

        $this->assertEquals(0, $passHolder->getPoints()->toNative());

        $this->assertTrue(
            $passHolder
                ->getPrivacyPreferences()
                ->sameValueAs(
                    new PrivacyPreferences(
                        PrivacyPreferenceEmail::NOTIFICATION(),
                        PrivacyPreferenceSMS::NOTIFICATION()
                    )
                )
        );

        $this->assertNull($passHolder->getINSZNumber());
        $this->assertNull($passHolder->getGender());
        $this->assertNull($passHolder->getNationality());
        $this->assertNull($passHolder->getPicture());
        $this->assertNull($passHolder->getContactInformation());
        $this->assertNull($passHolder->getKansenStatuten());
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json()
    {
        $passHolder = PassHolder::fromCultureFeedPassHolder($this->cfPassHolderFull);
        $json = json_encode($passHolder);
        $this->assertJsonEquals($json, 'PassHolder/data/passholder-complete.json');
    }

    /**
     * @test
     */
    public function it_omits_empty_properties_from_json()
    {
        $passHolder = PassHolder::fromCultureFeedPassHolder($this->cfPassHolderMinimal);
        $json = json_encode($passHolder);
        $this->assertJsonEquals($json, 'PassHolder/data/passholder-minimum.json');
    }
}
