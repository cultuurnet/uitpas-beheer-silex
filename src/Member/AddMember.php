<?php

namespace CultuurNet\UiTPASBeheer\Member;

use ValueObjects\Web\EmailAddress;

final class AddMember
{
    /**
     * @var EmailAddress
     */
    private $email;

    /**
     * @param EmailAddress $email
     */
    public function __construct(EmailAddress $email)
    {
        $this->email = $email;
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress()
    {
        return $this->email;
    }
}
