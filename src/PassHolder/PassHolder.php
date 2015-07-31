<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\INSZNumber;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferences;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

final class PassHolder implements \JsonSerializable
{
    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var BirthInformation
     */
    protected $birthInformation;

    /**
     * @var INSZNumber
     */
    protected $inszNumber;

    /**
     * @var Gender|null
     */
    protected $gender;

    /**
     * @var StringLiteral
     */
    protected $nationality;

    /**
     * @var StringLiteral|null
     */
    protected $picture;

    /**
     * @var ContactInformation|null
     */
    protected $contactInformation;

    /**
     * @var PrivacyPreferences
     */
    protected $privacyPreferences;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $points;

    /**
     * @param Name $name
     * @param Address $address
     * @param BirthInformation $birthInformation
     */
    public function __construct(
        Name $name,
        Address $address,
        BirthInformation $birthInformation
    ) {
        $this->name = $name;
        $this->address = $address;
        $this->birthInformation = $birthInformation;

        $this->points = new Integer(0);
    }

    /**
     * @param INSZNumber $inszNumber
     * @return PassHolder
     */
    public function withINSZNumber(INSZNumber $inszNumber)
    {
        return $this->with('inszNumber', $inszNumber);
    }

    /**
     * @param Gender $gender
     * @return PassHolder
     */
    public function withGender(Gender $gender)
    {
        return $this->with('gender', $gender);
    }

    /**
     * @param StringLiteral $nationality
     * @return PassHolder
     */
    public function withNationality(StringLiteral $nationality)
    {
        return $this->with('nationality', $nationality);
    }

    /**
     * @param StringLiteral $picture
     * @return PassHolder
     */
    public function withPicture(StringLiteral $picture)
    {
        return $this->with('picture', $picture);
    }

    /**
     * @param ContactInformation $contactInformation
     * @return PassHolder
     */
    public function withContactInformation(ContactInformation $contactInformation)
    {
        return $this->with('contactInformation', $contactInformation);
    }

    /**
     * @param PrivacyPreferences $preferences
     * @return PassHolder
     */
    public function withPrivacyPreferences(PrivacyPreferences $preferences)
    {
        return $this->with('privacyPreferences', $preferences);
    }

    /**
     * @param \ValueObjects\Number\Integer $points
     * @return PassHolder
     */
    public function withPoints(Integer $points)
    {
        return $this->with('points', $points);
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return PassHolder
     */
    private function with($property, $value)
    {
        $c = clone $this;
        $c->{$property} = $value;
        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'birth' => $this->birthInformation,
        ];

        if (!is_null($this->inszNumber)) {
            $data['inszNumber'] = $this->inszNumber->toNative();
        }

        if (!is_null($this->gender)) {
            $data['gender'] = $this->gender->toNative();
        }
        if (!is_null($this->nationality)) {
            $data['nationality'] = $this->nationality->toNative();
        }
        if (!is_null($this->picture)) {
            $data['picture'] = $this->picture->toNative();
        }
        if (!is_null($this->contactInformation) &&
            !empty($this->contactInformation->jsonSerialize())) {
            $data['contact'] = $this->contactInformation;
        }
        if (!is_null($this->privacyPreferences)) {
            $data['privacy'] = $this->privacyPreferences;
        }
        if (!is_null($this->points)) {
            $data['points'] = $this->points->toNative();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     * @return PassHolder
     */
    public static function fromCultureFeedPassHolder(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        $name = Name::fromCultureFeedPassHolder($cfPassHolder);
        $address = Address::fromCultureFeedPassHolder($cfPassHolder);
        $birthInformation = BirthInformation::fromCultureFeedPassHolder($cfPassHolder);

        $passHolder = new PassHolder($name, $address, $birthInformation);

        if (!empty($cfPassHolder->inszNumber)) {
            $passHolder = $passHolder->withINSZNumber(
                new INSZNumber((string) $cfPassHolder->inszNumber)
            );
        }

        if (!empty($cfPassHolder->gender)) {
            $passHolder = $passHolder->withGender(
                Gender::get($cfPassHolder->gender)
            );
        }

        if (!empty($cfPassHolder->nationality)) {
            $passHolder = $passHolder->withNationality(
                new StringLiteral($cfPassHolder->nationality)
            );
        }

        if (!empty($cfPassHolder->picture)) {
            $passHolder = $passHolder->withPicture(
                new StringLiteral($cfPassHolder->picture)
            );
        }

        if (!empty($cfPassHolder->points)) {
            $passHolder = $passHolder->withPoints(
                new Integer($cfPassHolder->points)
            );
        }

        $contactInformation = ContactInformation::fromCultureFeedPassHolder($cfPassHolder);
        $passHolder = $passHolder->withContactInformation($contactInformation);

        $passHolder = $passHolder->withPrivacyPreferences(
            PrivacyPreferences::fromCultureFeedPassHolder($cfPassHolder)
        );

        return $passHolder;
    }
}
