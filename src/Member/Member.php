<?php

namespace CultuurNet\UiTPASBeheer\Member;

use CultuurNet\UiTPASBeheer\Properties\Uid;
use ValueObjects\StringLiteral\StringLiteral;

final class Member implements \JsonSerializable
{
    /**
     * @var Uid
     */
    private $uid;

    /**
     * @var StringLiteral
     */
    private $nick;

    /**
     * @var MemberRole
     */
    private $role;

    /**
     * @param Uid $uid
     * @param StringLiteral $nick
     * @param MemberRole $role
     */
    public function __construct(
        Uid $uid,
        StringLiteral $nick,
        MemberRole $role
    ) {
        $this->uid = $uid;
        $this->nick = $nick;
        $this->role = $role;
    }

    /**
     * @return Uid
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return StringLiteral
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * @return MemberRole
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'uid' => $this->uid->toNative(),
            'nick' => $this->nick->toNative(),
            'role' => $this->role->toNative(),
        ];
    }

    /**
     * @param \CultureFeed_Uitpas_Counter_Member $cfMember
     * @param MemberRole $role
     * @return Member
     */
    public static function fromCultureFeedCounterMember(
        \CultureFeed_Uitpas_Counter_Member $cfMember,
        MemberRole $role
    ) {
        return new Member(
            new Uid($cfMember->id),
            new StringLiteral($cfMember->nick),
            $role
        );
    }
}
