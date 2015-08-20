<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     *
     * @return Registration
     *
     * @throws MissingPropertyException
     *   When a required property is missing.
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->activityId)) {
            throw new MissingPropertyException('activityId');
        }
        if (!isset($data->priceClass)) {
            throw new MissingPropertyException('priceClass');
        }

        $activityId = new StringLiteral((string) $data->activityId);
        $priceClass = new PriceClass((string) $data->priceClass);

        $registration = new Registration(
            $activityId,
            $priceClass
        );

        if (isset($data->tariffId)) {
            $registration = $registration->withTariffId(
                new StringLiteral((string) $data->tariffId)
            );
        }

        return $registration;
    }
}
