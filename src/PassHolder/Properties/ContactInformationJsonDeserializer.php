<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class ContactInformationJsonDeserializer extends JSONDeserializer
{
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        $contactInformation = new ContactInformation();

        if (isset($data->email)) {
            $contactInformation = $contactInformation->withEmail(
                new EmailAddress((string) $data->email)
            );
        }

        if (isset($data->telephoneNumber)) {
            $contactInformation = $contactInformation->withTelephoneNumber(
                new StringLiteral((string) $data->telephoneNumber)
            );
        }

        if (isset($data->mobileNumber)) {
            $contactInformation = $contactInformation->withMobileNumber(
                new StringLiteral((string) $data->mobileNumber)
            );
        }

        return $contactInformation;
    }
}
