<?php

namespace CultuurNet\UiTPASBeheer\Member;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;

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
}
