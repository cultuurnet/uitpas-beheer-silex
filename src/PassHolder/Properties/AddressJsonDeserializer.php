<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class AddressJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     * @return Address
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->postalCode)) {
            throw new MissingPropertyException('postalCode');
        }

        if (empty($data->city)) {
            throw new MissingPropertyException('city');
        }

        $address = new Address(
            new StringLiteral((string) $data->postalCode),
            new StringLiteral((string) $data->city)
        );

        if (isset($data->street)) {
            $address = $address->withStreet(
                new StringLiteral((string) $data->street)
            );
        }

        return $address;
    }
}
