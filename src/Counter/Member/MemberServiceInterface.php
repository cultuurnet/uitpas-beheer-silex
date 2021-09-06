<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use CultuurNet\UiTPASBeheer\User\UserNotFoundException;
use ValueObjects\Web\EmailAddress;

interface MemberServiceInterface
{
    /**
     * @return Member[]
     */
    public function all();

    public function add(EmailAddress $emailAddress);

    /**
     * @param Uid $uid
     */
    public function remove(Uid $uid);
}
