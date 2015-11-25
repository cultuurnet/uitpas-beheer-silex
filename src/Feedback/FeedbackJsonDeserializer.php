<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

class FeedbackJsonDeserializer extends JSONDeserializer
{
    /**
     * @param StringLiteral $data
     *
     * @return Feedback
     *
     * @throws IncorrectParameterValueException
     *   When e-mail address is invalid.
     *
     * @throws MissingPropertyException
     *   When a required property is missing.
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->name)) {
            throw new MissingPropertyException('name');
        }
        if (empty($data->counter)) {
            throw new MissingPropertyException('counter');
        }
        if (empty($data->email)) {
            throw new MissingPropertyException('email');
        }
        if (empty($data->message)) {
            throw new MissingPropertyException('message');
        }

        $name = new StringLiteral((string) $data->name);
        $counter = new StringLiteral((string) $data->counter);
        $message = new StringLiteral((string) $data->message);

        try {
            $email = new EmailAddress($data->email);
        } catch (\InvalidArgumentException $e) {
            throw new IncorrectParameterValueException('email');
        }

        return new Feedback(
            $name,
            $email,
            $counter,
            $message
        );
    }
}
