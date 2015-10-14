<?php

namespace CultuurNet\UiTPASBeheer\User;

use CultuurNet\UiTIDProvider\User\User;
use ValueObjects\Web\EmailAddress;

interface UserServiceInterface
{
    /**
     * @param EmailAddress $email
     *
     * @return User
     *
     * @throws UserNotFoundException
     *   When no user was found for the given email address.
     */
    public function getUserByEmail(EmailAddress $email);
}
