<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Registration;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Uid;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializer extends JSONDeserializer
{
    /**
     * @var KansenStatuutJsonDeserializer
     */
    protected $kansenStatuutJsonDeserializer;

    /**
     * @param KansenStatuutJsonDeserializer $kansenStatuutJsonDeserializer
     */
    public function __construct(KansenStatuutJsonDeserializer $kansenStatuutJsonDeserializer)
    {
        $this->kansenStatuutJsonDeserializer = $kansenStatuutJsonDeserializer;
    }

    /**
     * @param StringLiteral $data
     * @return Registration
     *
     * @throws MissingPropertyException
     *   When passholderUid or reason are missing.
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->passholderUid)) {
            throw new MissingPropertyException('passholderUid');
        }
        if (empty($data->reason)) {
            throw new MissingPropertyException('reason');
        }

        $registration = new Registration(
            new Uid((string) $data->passholderUid),
            PurchaseReason::get($data->reason)
        );

        if (isset($data->kansenStatuut)) {
            $registration = $registration->withKansenStatuut(
                $this->kansenStatuutJsonDeserializer->deserialize(
                    new StringLiteral(
                        json_encode($data->kansenStatuut)
                    )
                )
            );
        }

        if (isset($data->voucherNumber)) {
            $registration = $registration->withVoucherNumber(
                new VoucherNumber((string) $data->voucherNumber)
            );
        }

        return $registration;
    }
}
