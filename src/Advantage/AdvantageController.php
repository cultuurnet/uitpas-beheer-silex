<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Exception\InternalErrorException;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
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
     * @param $uitpasNumber
     * @param $advantageIdentifier
     *
     * @return JsonResponse
     *
     * @throws InternalErrorException
     *   When no advantage service was found for the advantage identifier's type.
     * @throws AdvantageNotFoundException
     *   When no advantage was found for the specified identifier.
     */
    public function get($uitpasNumber, $advantageIdentifier)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $advantageIdentifier = new AdvantageIdentifier($advantageIdentifier);

        $service = $this->getAdvantageServiceForType($advantageIdentifier->getType());

        $advantage = $service->get(
            $uitpasNumber,
            $advantageIdentifier->getId()
        );

        if (is_null($advantage)) {
            throw new AdvantageNotFoundException($advantageIdentifier);
        }

        return JsonResponse::create()
            ->setData($advantage)
            ->setPrivate(true);
    }

    /**
     * @param string $uitpasNumber
     *
     * @return JsonResponse
     */
    public function getExchangeable($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $advantages = array();
        foreach ($this->advantageServices as $advantageService) {
            $advantages = array_merge(
                $advantages,
                $advantageService->getExchangeable($uitpasNumber)
            );
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
     * @throws ReadableCodeResponseException
     *   When a CultureFeed error occurred.
     * @throws AdvantageNotFoundException
     *   When no advantage was found for the specified advantage identifier.
     * @throws InternalErrorException
     *   When no advantage was found for the specified identifier.
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
            throw ReadableCodeResponseException::fromCultureFeedException($exception);
        }

        return $this->get(
            $uitpasNumber->toNative(),
            $advantageIdentifier->toNative()
        );
    }
}
