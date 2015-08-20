<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializer extends JSONDeserializer
{
    /**
     * @var DeserializerInterface
     */
    protected $passholderJsonDeserializer;

    public function __construct(
        DeserializerInterface $passholderJsonDeserializer
    ) {
        $this->passholderJsonDeserializer = $passholderJsonDeserializer;
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
        /**
         * @var Passholder $passholder
         */
        $passholder = null;
        $voucherNumber = null;

        // passholder
        if (empty($data->passholder)) {
            throw new MissingPropertyException('passholder');
        }
        try {
            $passholder = $this->passholderJsonDeserializer->deserialize(
                new StringLiteral(json_encode($data->passholder))
            );
        } catch (MissingPropertyException $e) {
            throw MissingPropertyException::fromMissingChildPropertyException('passholder', $e);
        }

        // optional voucher number
        if (!empty($data->voucherNumber)) {
            $voucherNumber = new VoucherNumber($data->voucherNumber);
        }

        return new Registration($passholder, $voucherNumber);
    }
}
