<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenStatuutJsonDeserializer;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializer extends JSONDeserializer
{
    /**
     * @var DeserializerInterface
     */
    protected $passHolderJsonDeserializer;

    /**
     * @var DeserializerInterface
     */
    protected $kansenStatuutJsonDeserializer;

    public function __construct(
        DeserializerInterface $passHolderJsonDeserializer,
        DeserializerInterface $kansenStatuutJsonDeserializer
    ) {
        $this->passHolderJsonDeserializer = $passHolderJsonDeserializer;
        $this->kansenStatuutJsonDeserializer = $kansenStatuutJsonDeserializer;
    }

    /**
     * @param StringLiteral $data
     * @return Registration
     *
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        // PassHolder.
        if (empty($data->passHolder)) {
            throw new MissingPropertyException('passHolder');
        }
        try {
            $passHolder = $this->passHolderJsonDeserializer->deserialize(
                new StringLiteral(json_encode($data->passHolder))
            );
            $registration = new Registration($passHolder);
        } catch (MissingPropertyException $e) {
            throw MissingPropertyException::fromMissingChildPropertyException('passHolder', $e);
        }

        // Optional voucher number.
        if (!empty($data->voucherNumber)) {
            $voucherNumber = new VoucherNumber($data->voucherNumber);
            $registration = $registration->withVoucherNumber($voucherNumber);
        }

        // Optional kansenstatuut info.
        if (!empty($data->kansenStatuut)) {
            try {
                $kansenStatuut = $this->kansenStatuutJsonDeserializer->deserialize(
                    new StringLiteral(json_encode($data->kansenStatuut))
                );
                $registration = $registration->withKansenstatuut($kansenStatuut);
            } catch (MissingPropertyException $e) {
                throw MissingPropertyException::fromMissingChildPropertyException('kansenStatuut', $e);
            }
        }

        return $registration;
    }
}
