<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class ContactInformationJsonDeserializer extends JSONDeserializer
{
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        $contactInformation = new ContactInformation();

        if (isset($data->email) && !empty($data->email)) {
            try {
                $contactInformation = $contactInformation->withEmail(
                    new EmailAddress((string) $data->email)
                );
            } catch (InvalidNativeArgumentException $exception) {
                throw new EmailAddressInvalidException((string) $data->email);
            }
        }

        if (isset($data->telephoneNumber) && !empty($data->telephoneNumber)) {
            $contactInformation = $contactInformation->withTelephoneNumber(
                new StringLiteral((string) $data->telephoneNumber)
            );
        }

        if (isset($data->mobileNumber) && !empty($data->mobileNumber)) {
            $contactInformation = $contactInformation->withMobileNumber(
                new StringLiteral((string) $data->mobileNumber)
            );
        }

        return $contactInformation;
    }
}
