<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutCollection;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\INSZNumber;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceEmail;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferences;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceSMS;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASCollection;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

final class PassHolder implements \JsonSerializable
{
    /**
     * @var Uid
     */
    protected $uid;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Remarks|null
     */
    protected $remarks;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var BirthInformation
     */
    protected $birthInformation;

    /**
     * @var INSZNumber|null
     */
    protected $inszNumber;

    /**
     * @var Gender|null
     */
    protected $gender;

    /**
     * @var StringLiteral|null
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
     * @var KansenStatuutCollection|null
     */
    protected $kansenStatuten;

    /**
     * @var PrivacyPreferences
     */
    protected $privacyPreferences;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $points;

    /**
     * @var UiTPASCollection|null
     */
    protected $uitpasCollection;

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

        $this->privacyPreferences = new PrivacyPreferences(
            PrivacyPreferenceEmail::NOTIFICATION(),
            PrivacyPreferenceSMS::NOTIFICATION()
        );
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return BirthInformation
     */
    public function getBirthInformation()
    {
        return $this->birthInformation;
    }

    /**
     * @param Uid $uid
     * @return PassHolder
     */
    public function withUid(Uid $uid)
    {
        return $this->with('uid', $uid);
    }

    /**
     * @return Uid
     */
    public function getUid()
    {
        return $this->uid;
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
     * @return INSZNumber|null
     */
    public function getINSZNumber()
    {
        return $this->inszNumber;
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
     * @return Gender|null
     */
    public function getGender()
    {
        return $this->gender;
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
     * @return StringLiteral|null
     */
    public function getNationality()
    {
        return $this->nationality;
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
     * @return StringLiteral|null
     */
    public function getPicture()
    {
        return $this->picture;
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
     * @return PassHolder
     */
    public function withoutContactInformation()
    {
        return $this->with('contactInformation', null);
    }

    /**
     * @return ContactInformation|null
     */
    public function getContactInformation()
    {
        return $this->contactInformation;
    }

    /**
     * @param KansenStatuutCollection $kansenStatuutCollection
     * @return PassHolder
     */
    public function withKansenStatuten(KansenStatuutCollection $kansenStatuutCollection)
    {
        return $this->with('kansenStatuten', $kansenStatuutCollection);
    }

    /**
     * @return KansenStatuutCollection|null
     */
    public function getKansenStatuten()
    {
        return $this->kansenStatuten;
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
     * @return PrivacyPreferences|null
     */
    public function getPrivacyPreferences()
    {
        return $this->privacyPreferences;
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
     * @return \ValueObjects\Number\Integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param UiTPASCollection $uitpasCollection
     * @return PassHolder
     */
    public function withUiTPASCollection(UiTPASCollection $uitpasCollection)
    {
        $c = clone $this;
        $c->uitpasCollection = $uitpasCollection;
        return $c;
    }

    /**
     * @return UiTPASCollection|null
     */
    public function getUiTPASCollection()
    {
        return $this->uitpasCollection;
    }

    /**
     * @return Remarks|null
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param Remarks $remarks
     * @return Passholder
     */
    public function withRemarks(Remarks $remarks)
    {
        return $this->with('remarks', $remarks);
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

        if (!is_null($this->uid)) {
            $data['uid'] = $this->uid->toNative();
        }

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

        if (!is_null($this->kansenStatuten)) {
            $data['kansenStatuten'] = array_values($this->kansenStatuten->jsonSerialize());
        }

        if (!is_null($this->privacyPreferences)) {
            $data['privacy'] = $this->privacyPreferences;
        }

        if (!is_null($this->points)) {
            $data['points'] = $this->points->toNative();
        }

        if (!is_null($this->uitpasCollection)) {
            $data['uitpassen'] = array_values($this->uitpasCollection->jsonSerialize());
        }

        if (!is_null($this->remarks)) {
            $data['remarks'] = $this->remarks->toNative();
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

        $passHolder = new PassHolder(
            $name,
            $address,
            $birthInformation
        );

        if (!empty($cfPassHolder->uitIdUser->id)) {
            $passHolder = $passHolder->withUid(
                new Uid((string) $cfPassHolder->uitIdUser->id)
            );
        }

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

        if (!empty($cfPassHolder->email) ||
            !empty($cfPassHolder->telephone) ||
            !empty($cfPassHolder->gsm)) {
            $passHolder = $passHolder->withContactInformation(
                ContactInformation::fromCultureFeedPassHolder($cfPassHolder)
            );
        }

        if (!empty($cfPassHolder->cardSystemSpecific)) {
            $kansenStatuutCollection = KansenStatuutCollection::fromCultureFeedPassholderCardSystemSpecific(
                $cfPassHolder->cardSystemSpecific
            );
            if ($kansenStatuutCollection->length() > 0) {
                $passHolder = $passHolder->withKansenStatuten($kansenStatuutCollection);
            }

            $uitpasCollection = UiTPASCollection::fromCultureFeedPassholderCardSystemSpecific(
                $cfPassHolder->cardSystemSpecific
            );
            if ($uitpasCollection->length() > 0) {
                $passHolder = $passHolder->withUiTPASCollection($uitpasCollection);
            }
        }

        if (!empty($cfPassHolder->moreInfo)) {
            $passHolder = $passHolder->withRemarks(new Remarks($cfPassHolder->moreInfo));
        }

        $passHolder = $passHolder->withPrivacyPreferences(
            PrivacyPreferences::fromCultureFeedPassHolder($cfPassHolder)
        );

        return $passHolder;
    }
}
