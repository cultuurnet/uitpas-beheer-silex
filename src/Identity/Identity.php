<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\Group\Group;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolder;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;

final class Identity implements \JsonSerializable
{
    /**
     * @var \CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS
     */
    protected $uitPas;

    /**
     * @var PassHolder
     */
    protected $passHolder;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @param \CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS $uitPas
     */
    public function __construct(UiTPAS $uitPas)
    {
        $this->uitPas = $uitPas;
    }

    /**
     * @param PassHolder $passHolder
     * @return Identity
     */
    public function withPassHolder(PassHolder $passHolder)
    {
        $c = clone $this;
        $c->passHolder = $passHolder;
        return $c;
    }

    public function withGroup(Group $group)
    {
        $c = clone $this;
        $c->group = $group;
        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'uitPas' => $this->uitPas,
        ];

        if (!is_null($this->passHolder)) {
            $data['passHolder'] = $this->passHolder;
        }

        if (!is_null($this->group)) {
            $data['group'] = $this->group;
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Identity $cfIdentity
     * @return Identity
     */
    public static function fromCultureFeedIdentity(\CultureFeed_Uitpas_Identity $cfIdentity)
    {
        $card = UiTPAS::fromCultureFeedPassHolderCard($cfIdentity->card);
        $identity = new Identity($card);

        if (!empty($cfIdentity->passHolder)) {
            $passHolder = PassHolder::fromCultureFeedPassHolder($cfIdentity->passHolder);
            $identity = $identity->withPassHolder($passHolder);
        }

        if (!empty($cfIdentity->groupPass)) {
            $group = Group::fromCultureFeedGroupPass($cfIdentity->groupPass);
            $identity = $identity->withGroup($group);
        }

        return $identity;
    }
}
