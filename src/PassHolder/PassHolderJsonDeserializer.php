<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\INSZNumber;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderJsonDeserializer extends JSONDeserializer
{
    /**
     * @var DeserializerInterface
     */
    protected $nameJsonDeserializer;

    /**
     * @var DeserializerInterface
     */
    protected $addressJsonDeserializer;

    /**
     * @var DeserializerInterface
     */
    protected $birthInformationJsonDeserializer;

    /**
     * @var DeserializerInterface
     */
    protected $contactInformationJsonDeserializer;

    /**
     * @var DeserializerInterface
     */
    protected $privacyPreferencesJsonDeserializer;

    public function __construct(
        DeserializerInterface $nameJsonDeserializer,
        DeserializerInterface $addressJsonDeserializer,
        DeserializerInterface $birthInformationJsonDeserializer,
        DeserializerInterface $contactInformationJsonDeserializer,
        DeserializerInterface $privacyPreferencesJsonDeserializer
    ) {
        $this->nameJsonDeserializer = $nameJsonDeserializer;
        $this->addressJsonDeserializer = $addressJsonDeserializer;
        $this->birthInformationJsonDeserializer = $birthInformationJsonDeserializer;
        $this->contactInformationJsonDeserializer = $contactInformationJsonDeserializer;
        $this->privacyPreferencesJsonDeserializer = $privacyPreferencesJsonDeserializer;
    }

    /**
     * @param StringLiteral $data
     * @return PassHolder
     *
     * @throws MissingPropertyException
     */
    public function deserialize(StringLiteral $data)
    {
        $data = parent::deserialize($data);

        if (empty($data->name)) {
            throw new MissingPropertyException('name');
        }
        if (empty($data->address)) {
            throw new MissingPropertyException('address');
        }
        if (empty($data->birth)) {
            throw new MissingPropertyException('birth');
        }

        try {
            $name = $this->nameJsonDeserializer->deserialize(
                new StringLiteral(json_encode($data->name))
            );
        } catch (MissingPropertyException $e) {
            throw MissingPropertyException::fromMissingChildPropertyException('name', $e);
        }

        try {
            $address = $this->addressJsonDeserializer->deserialize(
                new StringLiteral(json_encode($data->address))
            );
        } catch (MissingPropertyException $e) {
            throw MissingPropertyException::fromMissingChildPropertyException('address', $e);
        }

        try {
            $birthInformation = $this->birthInformationJsonDeserializer->deserialize(
                new StringLiteral(json_encode($data->birth))
            );
        } catch (MissingPropertyException $e) {
            throw MissingPropertyException::fromMissingChildPropertyException('birth', $e);
        }

        $passHolder = new PassHolder(
            $name,
            $address,
            $birthInformation
        );

        if (isset($data->inszNumber)) {
            $passHolder = $passHolder->withINSZNumber(
                new INSZNumber((string) $data->inszNumber)
            );
        }

        if (isset($data->remarks)) {
            $passHolder = $passHolder->withRemarks(
                new Remarks((string) $data->remarks)
            );
        }

        if (isset($data->gender)) {
            $passHolder = $passHolder->withGender(
                Gender::get((string) $data->gender)
            );
        }

        if (isset($data->nationality)) {
            $passHolder = $passHolder->withNationality(
                new StringLiteral((string) $data->nationality)
            );
        }

        if (isset($data->contact)) {
            try {
                $passHolder = $passHolder->withContactInformation(
                    $this->contactInformationJsonDeserializer->deserialize(
                        new StringLiteral(json_encode($data->contact))
                    )
                );
            } catch (MissingPropertyException $e) {
                throw MissingPropertyException::fromMissingChildPropertyException('contact', $e);
            }
        }

        if (isset($data->privacy)) {
            try {
                $passHolder = $passHolder->withPrivacyPreferences(
                    $this->privacyPreferencesJsonDeserializer->deserialize(
                        new StringLiteral(json_encode($data->privacy))
                    )
                );
            } catch (MissingPropertyException $e) {
                throw MissingPropertyException::fromMissingChildPropertyException('privacy', $e);
            }
        }

        if (!empty($data->picture)) {
            $passHolder = $passHolder->withPicture(
                new StringLiteral($data->picture)
            );
        }

        return $passHolder;
    }
}
