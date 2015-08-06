<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use ValueObjects\StringLiteral\StringLiteral;

class UiTPASTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $number = '0930000420206';

    /**
     * @var string
     */
    protected $city = 'Leuven';

    /**
     * @var \CultureFeed_Uitpas_Passholder
     */
    protected $passholderCardMinimal;

    /**
     * @var \CultureFeed_Uitpas_Passholder
     */
    protected $passholderCardFull;

    public function setUp()
    {
        $this->passholderCardMinimal = new \CultureFeed_Uitpas_Passholder_Card();
        $this->passholderCardMinimal->status = UiTPASStatus::ACTIVE;
        $this->passholderCardMinimal->uitpasNumber = $this->number;

        $this->passholderCardFull = clone $this->passholderCardMinimal;
        $this->passholderCardFull->city = $this->city;
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder_card()
    {
        $cfPassHolderCard = $this->passholderCardFull;

        $uitpas = UiTPAS::fromCultureFeedPassHolderCard($cfPassHolderCard);
        $this->assertAttributeEquals(new UiTPASNumber($this->number), 'number', $uitpas);
        $this->assertAttributeEquals(UiTPASStatus::ACTIVE, 'status', $uitpas);
        $this->assertAttributeEquals(new StringLiteral($this->city), 'city', $uitpas);
    }

    /**
     * @test
     */
    public function it_can_manage_missing_properties_while_extracting_properties_from_a_culturefeed_passholder_card()
    {
        $cfPassHolderCard = $this->passholderCardMinimal;

        $uitpas = UiTPAS::fromCultureFeedPassHolderCard($cfPassHolderCard);
        $this->assertAttributeEquals(new UiTPASNumber($this->number), 'number', $uitpas);
        $this->assertAttributeEquals(UiTPASStatus::ACTIVE, 'status', $uitpas);
        $this->assertAttributeEmpty('city', $uitpas);
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json()
    {
        $cfPassHolderCard = $this->passholderCardFull;
        $uitpas = UiTPAS::fromCultureFeedPassHolderCard($cfPassHolderCard);

        $data = $uitpas->jsonSerialize();
        $this->assertArrayHasKey('number', $data, 'The number key is missing.');
        $this->assertArrayHasKey('kansenStatuut', $data, 'The kansenStatuut key is missing.');
        $this->assertArrayHasKey('status', $data, 'The status key is missing.');
        $this->assertArrayHasKey('city', $data, 'The city key is missing.');
    }

    /**
     * @test
     */
    public function it_can_manage_missing_properties_while_serializing_to_json()
    {
        $cfPassHolderCard = $this->passholderCardMinimal;
        $uitpas = UiTPAS::fromCultureFeedPassHolderCard($cfPassHolderCard);

        $data = $uitpas->jsonSerialize();
        $this->assertArrayHasKey('number', $data, 'The number key is missing.');
        $this->assertArrayHasKey('kansenStatuut', $data, 'The kansenStatuut key is missing.');
        $this->assertArrayHasKey('status', $data, 'The status key is missing.');
        $this->assertArrayNotHasKey('city', $data, 'The city key should not be present.');
    }
}
