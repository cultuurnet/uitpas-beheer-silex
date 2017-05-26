<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Exception\InternalErrorException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberInvalidException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\StringLiteral\StringLiteral;

class AdvantageController
{
    /**
     * @var AdvantageServiceInterface[]
     */
    protected $advantageServices;

    /**
     * @var DeserializerInterface
     */
    protected $advantageIdentifierJsonDeserializer;

    /**
     * @param DeserializerInterface $advantageIdentifierJsonDeserializer
     */
    public function __construct(DeserializerInterface $advantageIdentifierJsonDeserializer)
    {
        $this->advantageIdentifierJsonDeserializer = $advantageIdentifierJsonDeserializer;
    }

    /**
     * @param AdvantageType $type
     * @param AdvantageServiceInterface $advantageService
     */
    public function registerAdvantageService(
        AdvantageType $type,
        AdvantageServiceInterface $advantageService
    ) {
        $typeKey = $type->toNative();
        $this->advantageServices[$typeKey] = $advantageService;
    }

    /**
     * @param AdvantageType $type
     *
     * @return AdvantageServiceInterface
     *
     * @throws InternalErrorException
     *   When no service was found for the provided type.
     */
    protected function getAdvantageServiceForType(AdvantageType $type)
    {
        $typeKey = $type->toNative();

        if (!isset($this->advantageServices[$typeKey])) {
            throw new InternalErrorException();
        }

        return $this->advantageServices[$typeKey];
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param AdvantageIdentifier $advantageIdentifier
     * @param bool $exchanged
     *   Was there an exchange action for this advantage?
     * @return JsonResponse
     * @throws InternalErrorException
     *   When no advantage service was found for the advantage identifier's type.
     * @throws AdvantageNotFoundException
     *   When no advantage was found for the specified advantage identifier.
     */
    protected function getAdvantageJsonResponse(UiTPASNumber $uitpasNumber, AdvantageIdentifier $advantageIdentifier, $exchanged = false)
    {
        $service = $this->getAdvantageServiceForType($advantageIdentifier->getType());

        $advantage = $service->get(
            $uitpasNumber,
            $advantageIdentifier->getId()
        );

        if (is_null($advantage)) {
            throw new AdvantageNotFoundException($advantageIdentifier);
        }

        // Welcome advantages should be forced to exchanged after exchanging.
        // Sometimes the server returns an old cashedIn value.
        if ($exchanged && $advantage instanceof WelcomeAdvantage) {
            $advantage = $advantage->withExchangeable(false);
        }

        return JsonResponse::create()
            ->setData($advantage)
            ->setPrivate(true);
    }

    /**
     * @param string $uitpasNumber
     * @param string $advantageIdentifier
     *
     * @return JsonResponse
     *
     * @throws UiTPASNumberInvalidException
     *   When no valid UiTPASNumber object can be constructed from the
     *   provided value.
     * @throws AdvantageIdentifierInvalidException
     *   When no valid AdvantageIdentifier object can be constructed from the
     *   provided value.
     * @throws InternalErrorException
     *   When no advantage service was found for the advantage identifier's type.
     * @throws AdvantageNotFoundException
     *   When no advantage was found for the specified identifier.
     */
    public function get($uitpasNumber, $advantageIdentifier)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $advantageIdentifier = new AdvantageIdentifier($advantageIdentifier);

        return $this->getAdvantageJsonResponse($uitpasNumber, $advantageIdentifier);
    }

    /**
     * @param string $uitpasNumber
     *
     * @throws UiTPASNumberInvalidException
     *   When no valid UiTPASNumber object can be constructed from the
     *   provided value.
     * @throws CompleteResponseException
     *   When a CultureFeed error occurred.
     *
     * @return JsonResponse
     */
    public function getExchangeable($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $advantages = array();
        try {
            // All advantages should be shown. API has paging, so set a high number.
            foreach ($this->advantageServices as $advantageService) {
                $advantages = array_merge(
                    $advantages,
                    $advantageService->getExchangeable($uitpasNumber, 999999)
                );
            }
        } catch (\CultureFeed_Exception $exception) {
            throw CompleteResponseException::fromCultureFeedException($exception);
        }

        return JsonResponse::create()
            ->setData($advantages)
            ->setPrivate(true);
    }

    /**
     * @param Request $request
     * @param $uitpasNumber
     *
     * @return JsonResponse
     *
     * @throws UiTPASNumberInvalidException
     *   When no valid UiTPASNumber object can be constructed from the
     *   provided value.
     * @throws MissingPropertyException
     *   When the request body lacks the required advantage 'id' property.
     * @throws AdvantageIdentifierInvalidException
     *   When no valid AdvantageIdentifier object can be constructed from the
     *   provided value.
     * @throws InternalErrorException
     *   When no advantage service was found for the advantage identifier's type.
     * @throws AdvantageNotFoundException
     *   When no advantage was found for the specified advantage identifier.
     * @throws CompleteResponseException
     *   When a CultureFeed error occurred.
     */
    public function exchange(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $content = new StringLiteral($request->getContent());

        /* @var AdvantageIdentifier $advantageIdentifier */
        $advantageIdentifier = $this->advantageIdentifierJsonDeserializer->deserialize($content);

        $service = $this->getAdvantageServiceForType($advantageIdentifier->getType());

        try {
            $service->exchange(
                $uitpasNumber,
                $advantageIdentifier->getId()
            );
        } catch (\CultureFeed_Exception $exception) {
            throw CompleteResponseException::fromCultureFeedException($exception);
        }

        return $this->getAdvantageJsonResponse($uitpasNumber, $advantageIdentifier, true);
    }
}
