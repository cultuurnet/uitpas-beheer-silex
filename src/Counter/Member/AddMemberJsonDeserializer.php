<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class AddMemberJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     *
     * @return AddMember
     *
     * @throws IncorrectParameterValueException
     *   When email is not in a valid format.
     *
     * @throws MissingPropertyException
     *   When email is missing.
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!isset($data->email)) {
            throw new MissingPropertyException('email');
        }

        try {
            /* @var EmailAddress $email */
            $email = EmailAddress::fromNative($data->email);
        } catch (InvalidNativeArgumentException $e) {
            throw new IncorrectParameterValueException('email');
        }

        return new AddMember($email);
    }
}
