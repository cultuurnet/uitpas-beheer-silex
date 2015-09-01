<?php

namespace CultuurNet\UiTPASBeheer\Membership\Registration;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $json
     * @return Registration
     *
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $json)
    {
        $data = parent::deserialize($json);

        if (!isset($data->associationId)) {
            throw new MissingPropertyException('associationId');
        }

        $registration = new Registration(
            new AssociationId((string) $data->associationId)
        );

        if (!empty($data->endDate)) {
            $registration = $registration->withEndDate(
                Date::fromNativeDateTime(
                    // @todo Use RFC3339 format instead of 'Y-m-d'.
                    \DateTime::createFromFormat(
                        'Y-m-d',
                        $data->endDate
                    )
                )
            );
        }

        return $registration;
    }
}
