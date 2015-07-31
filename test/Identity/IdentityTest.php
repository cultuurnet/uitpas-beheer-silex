<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolder;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class IdentityTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Identity
     */
    protected $identity;

    /**
     * @var Identity
     */
    protected $identityWithPassHolder;

    /**
     * @var PassHolder
     */
    protected $passHolder;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var StringLiteral
     */
    protected $firstName;

    /**
     * @var StringLiteral
     */
    protected $lastName;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var StringLiteral
     */
    protected $postalCode;

    /**
     * @var StringLiteral
     */
    protected $city;

    /**
     * @var BirthInformation
     */
    protected $birthInformation;

    /**
     * @var Date
     */
    protected $dateOfBirth;

    /**
     * @var UiTPAS
     */
    protected $activeUitpas;

    /**
     * @var UiTPAS
     */
    protected $localStockUitpas;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    public function setUp()
    {
        $this->firstName = new StringLiteral('Layla');
        $this->lastName = new StringLiteral('Zyrani');
        $this->name = new Name($this->firstName, $this->lastName);

        $this->postalCode = new StringLiteral('1090');
        $this->city = new StringLiteral('Jette (Brussel)');
        $this->address = new Address($this->postalCode, $this->city);

        $dateTime = new \DateTime('1976-08-13');
        $this->dateOfBirth = Date::fromNativeDateTime($dateTime);
        $this->birthInformation = new BirthInformation($this->dateOfBirth);

        $this->passHolder = new PassHolder(
            $this->name,
            $this->address,
            $this->birthInformation
        );

        $this->uitpasNumber = new UiTPASNumber('1000000035419');

        $this->localStockUitpas = new UiTPAS(
            $this->uitpasNumber,
            UiTPASStatus::LOCAL_STOCK()
        );

        $this->identity = new Identity($this->localStockUitpas);

        $this->activeUitpas = new UiTPAS(
            $this->uitpasNumber,
            UiTPASStatus::ACTIVE()
        );

        $this->identityWithPassHolder = (new Identity($this->activeUitpas))
            ->withPassHolder($this->passHolder);
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $json = json_encode($this->identityWithPassHolder);
        $this->assertJsonEquals($json, 'Identity/data/identity-passholder.json');
    }

    /**
     * @test
     */
    public function it_omits_optional_properties_from_json()
    {
        $json = json_encode($this->identity);
        $this->assertJsonEquals($json, 'Identity/data/identity-minimum.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_cf_identity_with_only_a_card()
    {
        $cfPassHolderCard = new \CultureFeed_Uitpas_Passholder_Card();
        $cfPassHolderCard->status = UiTPASStatus::LOCAL_STOCK();
        $cfPassHolderCard->uitpasNumber = $this->uitpasNumber->toNative();
        $cfPassHolderCard->kansenpas = $this->uitpasNumber->hasKansenStatuut();

        $cfIdentity = new \CultureFeed_Uitpas_Identity();
        $cfIdentity->card = $cfPassHolderCard;

        $identity = Identity::fromCultureFeedIdentity($cfIdentity);

        $json = json_encode($identity);
        $this->assertJsonEquals($json, 'Identity/data/identity-minimum.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_cf_identity_with_a_passholder()
    {
        $cfPassHolderCard = new \CultureFeed_Uitpas_Passholder_Card();
        $cfPassHolderCard->status = UiTPASStatus::ACTIVE();
        $cfPassHolderCard->uitpasNumber = $this->uitpasNumber->toNative();
        $cfPassHolderCard->kansenpas = $this->uitpasNumber->hasKansenStatuut();

        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->firstName = $this->firstName->toNative();
        $cfPassHolder->name = $this->lastName->toNative();
        $cfPassHolder->city = $this->city->toNative();
        $cfPassHolder->postalCode = $this->postalCode->toNative();
        $cfPassHolder->dateOfBirth = $this->dateOfBirth
            ->toNativeDateTime()
            ->getTimestamp();

        $cfIdentity = new \CultureFeed_Uitpas_Identity();
        $cfIdentity->card = $cfPassHolderCard;
        $cfIdentity->passHolder = $cfPassHolder;

        $identity = Identity::fromCultureFeedIdentity($cfIdentity);

        $json = json_encode($identity);
        $this->assertJsonEquals($json, 'Identity/data/identity-passholder.json');
    }
}
