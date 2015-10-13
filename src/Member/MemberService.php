<?php

namespace CultuurNet\UiTPASBeheer\Member;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use CultuurNet\UiTPASBeheer\User\UserNotFoundException;

class MemberService extends CounterAwareUitpasService implements MemberServiceInterface
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

        if (isset($cfMembers['admins'])) {
            foreach ($cfMembers['admins'] as $cfMember) {
                $members[] = Member::fromCultureFeedCounterMember(
                    $cfMember,
                    MemberRole::ADMIN()
                );
            }
        }

        if (isset($cfMembers['members'])) {
            foreach ($cfMembers['members'] as $cfMember) {
                $members[] = Member::fromCultureFeedCounterMember(
                    $cfMember,
                    MemberRole::MEMBER()
                );
            }
        }

        return $members;
    }

    /**
     * @param Uid $uid
     *
     * @throws UserNotFoundException
     *   When no user was found for the given email address.
     */
    public function add(Uid $uid)
    {
        $this->getUitpasService()->addMemberToCounter(
            $uid->toNative(),
            $this->getCounterConsumerKey()
        );
    }

    /**
     * @param Uid $uid
     */
    public function remove(Uid $uid)
    {
        $this->getUitpasService()->removeMemberFromCounter(
            $uid->toNative(),
            $this->getCounterConsumerKey()
        );
    }
}
