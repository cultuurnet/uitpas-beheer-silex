<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\Group\Group;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolder;
use CultuurNet\UiTPASBeheer\UiTPAS\Filter\UiTPASFilterInterface;
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
     * @return UiTPAS
     */
    public function getUiTPAS()
    {
        return $this->uitPas;
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

    /**
     * @return PassHolder
     */
    public function getPassHolder()
    {
        return $this->passHolder;
    }

    /**
     * @param Group $group
     * @return Identity
     */
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

    /**
     * @param PassHolder $passHolder
     *
     * @param UiTPASFilterInterface|null $uitpasFilter
     *   The first UiTPAS in the collection after filtering will be used as
     *   the identity's UiTPAS. If no filter is provided, or the collection is
     *   empty after filtering, the first UiTPAS from the passholder will be
     *   used.
     *
     * @return Identity
     */
    public static function fromPassHolderWithUitpasCollection(
        PassHolder $passHolder,
        UiTPASFilterInterface $uitpasFilter = null
    ) {
        $uitpasCollection = $passHolder->getUiTPASCollection();

        if (is_null($uitpasCollection) || $uitpasCollection->length() === 0) {
            throw new \InvalidArgumentException(
                'PassHolder should have a UiTPASCollection with at least one UiTPAS.'
            );
        }

        if (!is_null($uitpasFilter)) {
            $filteredCollection = $uitpasFilter->filter(
                $passHolder->getUiTPASCollection()
            );

            if ($filteredCollection->length() > 0) {
                $uitpasCollection = $filteredCollection;
            }
        }

        $uitpasArray = $uitpasCollection->toArray();
        $uitpas = reset($uitpasArray);

        return (new Identity($uitpas))
            ->withPassHolder($passHolder);
    }
}
