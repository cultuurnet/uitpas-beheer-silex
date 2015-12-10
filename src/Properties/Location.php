<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use ValueObjects\StringLiteral\StringLiteral;
use CultuurNet\UiTPASBeheer\Properties\Address;

final class Location implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $name;

    /**
     * @var Address
     */
    protected $address;

    /**
     * Location constructor.
     * @param \ValueObjects\StringLiteral\StringLiteral $name
     * @param Address $address
     */
    public function __construct(
        StringLiteral $name,
        Address $address
    ) {
        $this->name = new StringLiteral(trim($name));
        $this->address = $address;
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

        $data['name'] = $this->name->toNative();
        $data['address'] = $this->address->jsonSerialize();

        return $data;
    }

    /**
     * @param \CultureFeed_Cdb_Data_Location $cfLocation
     * @return self
     */
    public static function fromCultureFeedCbdDataLocation(\CultureFeed_Cdb_Data_Location $cfLocation)
    {
        $name = new StringLiteral($cfLocation->getLabel());

        $physicalAddress = $cfLocation->getAddress()->getPhysicalAddress();
        $address = new Address(
            new StringLiteral($physicalAddress->getZip()),
            new StringLiteral($physicalAddress->getCity())
        );

        $streetAndNumber = $physicalAddress->getStreet() . ' ' . $physicalAddress->getHouseNumber();
        $address = $address->withStreet(new StringLiteral($streetAndNumber));

        $location = new self($name, $address);

        return $location;
    }
}
