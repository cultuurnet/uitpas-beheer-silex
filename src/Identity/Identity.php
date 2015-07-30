<?php

namespace CultuurNet\UiTPASBeheer\Identity;

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

        return $identity;
    }
}
