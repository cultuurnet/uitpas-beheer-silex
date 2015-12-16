<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use ValueObjects\StringLiteral\StringLiteral;
use CultuurNet\UiTPASBeheer\Properties\Address;

final class Location implements \JsonSerializable
{
    /**
     * @var StringLiteral|null
     */
    protected $name;

    /**
     * @var Address|null
     */
    protected $address;

    /**
     * @param StringLiteral $name
     * @return Location
     */
    public function withName(StringLiteral $name)
    {
        $c = clone $this;
        $c->name = new StringLiteral(trim($name));
        return $c;
    }

    /**
     * @param Address $address
     * @return Location
     */
    public function withAddress(Address $address)
    {
        $c = clone $this;
        $c->address = $address;
        return $c;
    }

    /**
     * @return StringLiteral
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
     * @param Location $other
     * @return bool
     */
    public function sameValueAs(Location $other)
    {
        return $this->jsonSerialize() === $other->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [];

        if (!is_null($this->name)) {
            $data['name'] = $this->name->toNative();
        }
        if (!is_null($this->address)) {
            $data['address'] = $this->address->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Cdb_Data_Location $cfLocation
     * @return self
     */
    public static function fromCultureFeedCbdDataLocation(\CultureFeed_Cdb_Data_Location $cfLocation)
    {
        $location = new self();

        $name = new StringLiteral($cfLocation->getLabel());
        if ($name) {
            $location->withName($name);
        }

        $physicalAddress = $cfLocation->getAddress()->getPhysicalAddress();
        if ($physicalAddress) {
            $address = new Address(
                new StringLiteral($physicalAddress->getZip()),
                new StringLiteral($physicalAddress->getCity())
            );

            $streetAndNumber = $physicalAddress->getStreet() . ' ' . $physicalAddress->getHouseNumber();
            $address = $address->withStreet(new StringLiteral($streetAndNumber));

            $location->withAddress($address);
        }

        return $location;
    }
}
