<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class CardSystemUpgradeJsonDeserializer extends JSONDeserializer
{
    /**
     * @var DeserializerInterface
     */
    protected $kansenStatuutJsonDeserializer;

    public function __construct(
        DeserializerInterface $kansenStatuutJsonDeserializer
    ) {
        $this->kansenStatuutJsonDeserializer = $kansenStatuutJsonDeserializer;
    }

    /**
     * @inheritdoc
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (!empty($data->cardSystemId)) {
            $cardSystemUpgrade = CardSystemUpgrade::withoutNewUiTPAS(
                new CardSystemId($data->cardSystemId)
            );
        } elseif (!empty($data->uitpasNumber)) {
            $kansenStatuut = null;
            if (!empty($data->kansenStatuut)) {
                $kansenStatuut = $this->kansenStatuutJsonDeserializer->deserialize(
                    new StringLiteral(
                        json_encode($data->kansenStatuut)
                    )
                );
            }

            $cardSystemUpgrade = CardSystemUpgrade::withNewUiTPAS(
                new UiTPASNumber($data->uitpasNumber),
                $kansenStatuut
            );
        } else {
            throw new MissingPropertyException('cardSystemId or uitpasNumber');
        }

        if (!empty($data->voucherNumber)) {
            $cardSystemUpgrade = $cardSystemUpgrade->withVoucherNumber(
                new VoucherNumber($data->voucherNumber)
            );
        }

        return $cardSystemUpgrade;
    }
}
