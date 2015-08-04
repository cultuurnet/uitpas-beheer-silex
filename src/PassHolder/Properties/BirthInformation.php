<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

final class BirthInformation implements \JsonSerializable
{
    /**
     * @var Date
     */
    protected $date;

    /**
     * @var StringLiteral|null
     */
    protected $place;

    /**
     * @param Date $date
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
    }

    /**
     * @return Date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param StringLiteral $place
     * @return BirthInformation
     */
    public function withPlace(StringLiteral $place)
    {
        $c = clone $this;
        $c->place = $place;
        return $c;
    }

    /**
     * @return StringLiteral|null
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param BirthInformation $other
     * @return bool
     */
    public function sameValueAs(BirthInformation $other)
    {
        return $this->jsonSerialize() === $other->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data['date'] = $this->date
            ->toNativeDateTime()
            ->format('Y-m-d');

        if (!is_null($this->place)) {
            $data['place'] = $this->place->toNative();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     * @return self
     */
    public static function fromCultureFeedPassHolder(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($cfPassHolder->dateOfBirth);
        $date = Date::fromNativeDateTime($dateTime);

        $birthInformation = new self($date);

        if (!empty($cfPassHolder->placeOfBirth)) {
            $birthInformation = $birthInformation->withPlace(
                new StringLiteral($cfPassHolder->placeOfBirth)
            );
        }

        return $birthInformation;
    }
}
