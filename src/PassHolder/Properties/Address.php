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
     * @param StringLiteral $postalCode
     * @param StringLiteral $city
     */
    public function __construct(
        StringLiteral $postalCode,
        StringLiteral $city
    ) {
        $this->postalCode = $postalCode;
        $this->city = $city;
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

        $address = new self($postalCode, $city);

        if (!empty($cfPassHolder->street)) {
            $address = $address->withStreet(
                new StringLiteral($cfPassHolder->street)
            );
        }

        return $address;
    }
}
