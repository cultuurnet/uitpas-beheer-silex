<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Kansenstatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenstatuutJsonDeserializer;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializer extends JSONDeserializer
{
    /**
     * @var DeserializerInterface
     */
    protected $passholderJsonDeserializer;

    /**
     * @var KansenstatuutJsonDeserializer
     */
    protected $kansenstatuutJsonDeserializer;

    public function __construct(
        DeserializerInterface $passholderJsonDeserializer,
        DeserializerInterface $kansenstatuutJsonDeserializer
    ) {
        $this->passholderJsonDeserializer = $passholderJsonDeserializer;
        $this->kansenstatuutJsonDeserializer = $kansenstatuutJsonDeserializer;
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

        // passholder
        if (empty($data->passholder)) {
            throw new MissingPropertyException('passholder');
        }
        try {
            $passholder = $this->passholderJsonDeserializer->deserialize(
                new StringLiteral(json_encode($data->passholder))
            );
            $registration = new Registration($passholder);
        } catch (MissingPropertyException $e) {
            throw MissingPropertyException::fromMissingChildPropertyException('passholder', $e);
        }

        // optional voucher number
        if (!empty($data->voucherNumber)) {
            $voucherNumber = new VoucherNumber($data->voucherNumber);
            $registration = $registration->withVoucherNumber($voucherNumber);
        }

        // optional kansenstatuut info
        if (!empty($data->kansenstatuut)) {
            try {
                $kansenstatuut = $this->kansenstatuutJsonDeserializer->deserialize(
                    new StringLiteral(json_encode($data->kansenstatuut))
                );
                $registration = $registration->withKansenstatuut($kansenstatuut);
            } catch (MissingPropertyException $e) {
                throw MissingPropertyException::fromMissingChildPropertyException('kansenstatuut', $e);
            }
        }

        return $registration;
    }
}
