<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\StringLiteral\StringLiteral;

final class Address implements \JsonSerializable
{
    /**
     * @var StringLiteral|null
     */
    protected $street;

    /**
     * @var StringLiteral
     */
    protected $postalCode;

    /**
     * @var StringLiteral
     */
    protected $city;

    /**
     * @var StringLiteral
     */
    protected $foreignCity;

    /**
     * @param StringLiteral $postalCode
     * @param StringLiteral $city
     * @param StringLiteral $foreignCity
     */
    public function __construct(
        StringLiteral $postalCode,
        StringLiteral $city,
        StringLiteral $foreignCity
    ) {
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->foreignCity = $foreignCity;
    }

    /**
     * @return StringLiteral
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @return StringLiteral
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return StringLiteral
     */
    public function getForeignCity()
    {
      return $this->foreignCity;
    }

    /**
     * @param StringLiteral $street
     * @return Address
     */
    public function withStreet(StringLiteral $street)
    {
        $c = clone $this;
        $c->street = $street;
        return $c;
    }

    /**
     * @return StringLiteral|null
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param Address $other
     * @return bool
     */
    public function sameValueAs(Address $other)
    {
        return $this->jsonSerialize() === $other->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [];

        if (!is_null($this->street)) {
            $data['street'] = $this->street->toNative();
        }

        $data['postalCode'] = $this->postalCode->toNative();
        $data['city'] = $this->city->toNative();
        $data['foreignCity'] = $this->foreignCity->toNative();

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder $cfPassHolder
     * @return self
     */
    public static function fromCultureFeedPassHolder(\CultureFeed_Uitpas_Passholder $cfPassHolder)
    {
        $postalCode = new StringLiteral($cfPassHolder->postalCode);
        $city = new StringLiteral($cfPassHolder->city);
        $foreignCity = new StringLiteral($cfPassHolder->foreignCity);

        $address = new self($postalCode, $city, $foreignCity);

        if (!empty($cfPassHolder->street)) {
            $address = $address->withStreet(
                new StringLiteral($cfPassHolder->street)
            );
        }

        return $address;
    }
}
