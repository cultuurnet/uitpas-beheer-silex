<?php

namespace CultuurNet\UiTPASBeheer\Member;

use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use CultuurNet\UiTPASBeheer\User\UserNotFoundException;

interface MemberServiceInterface
{
    /**
     * @return Member[]
     */
    public function all();

    /**
     * @param Uid $uid
     *
     * @throws UserNotFoundException
     *   When no user was found for the given email address.
     */
    public function add(Uid $uid);

    /**
     * @param Uid $uid
     */
    public function remove(Uid $uid);
}
