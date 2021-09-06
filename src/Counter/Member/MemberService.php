<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use CultureFeed_OAuthClient;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use ValueObjects\Web\EmailAddress;

class MemberService extends CounterAwareUitpasService implements MemberServiceInterface
{
    /**
     * @var CultureFeed_OAuthClient
     */
    private $oauthClient;

    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CultureFeed_OAuthClient $oauthClient,
        CounterConsumerKey $counterConsumerKey
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);
        $this->oauthClient = $oauthClient;
    }

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

    public function add(EmailAddress $emailAddress)
    {
        $data = [
            'email' => $emailAddress->toNative(),
            'balieConsumerKey' => $this->getCounterConsumerKey(),
        ];

        $this->oauthClient->authenticatedPost('uitpas/balie/member', $data);
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
