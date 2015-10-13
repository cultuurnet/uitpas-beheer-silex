<?php

namespace CultuurNet\UiTPASBeheer\Member;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use CultuurNet\UiTPASBeheer\User\UserNotFoundException;
use ValueObjects\StringLiteral\StringLiteral;

class MemberService extends CounterAwareUitpasService
{
    /**
     * @return Member[]
     */
    public function all()
    {
        $members = array();

        $cfMembers = $this->getUitpasService()->getMembersForCounter(
            $this->getCounterConsumerKey()
        );

        foreach ($cfMembers['admin'] as $cfMember) {
            $members[] = Member::fromCultureFeedCounterMember(
                $cfMember,
                MemberRole::ADMIN()
            );
        }

        foreach ($cfMembers['member'] as $cfMember) {
            $members[] = Member::fromCultureFeedCounterMember(
                $cfMember,
                MemberRole::MEMBER()
            );
        }

        return $members;
    }

    /**
     * @param \CultureFeed_User $cfUser
     *
     * @return Member
     *
     * @throws UserNotFoundException
     *   When no user was found for the given email address.
     */
    public function add(\CultureFeed_User $cfUser)
    {
        $this->getUitpasService()->addMemberToCounter(
            $cfUser->id,
            $this->getCounterConsumerKey()
        );

        return new Member(
            new Uid($cfUser->id),
            new StringLiteral($cfUser->nick),
            MemberRole::MEMBER()
        );
    }

    /**
     * @param Uid $uid
     */
    public function remove(Uid $uid)
    {
        $this->getUitpasService()->removeMemberFromCounter(
            $uid->toNative()
        );
    }
}
