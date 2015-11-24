<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

final class Feedback
{
    /**
     * @var StringLiteral
     */
    private $name;

    /**
     * @var EmailAddress
     */
    private $email;

    /**
     * @var StringLiteral
     */
    private $counterName;

    /**
     * @var StringLiteral
     */
    private $message;

    /**
     * @param StringLiteral $name
     * @param EmailAddress $email
     * @param StringLiteral $counterName
     * @param StringLiteral $message
     */
    public function __construct(
        StringLiteral $name,
        EmailAddress $email,
        StringLiteral $counterName,
        StringLiteral $message
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->counterName = $counterName;
        $this->message = $message;
    }

    /**
     * @return StringLiteral
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return EmailAddress
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return StringLiteral
     */
    public function getCounterName()
    {
        return $this->counterName;
    }

    /**
     * @return StringLiteral
     */
    public function getMessage()
    {
        return $this->message;
    }
}
