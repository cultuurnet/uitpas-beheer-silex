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
     * @param AdvantageServiceInterface $advantageService
     */
    public function registerAdvantageService(AdvantageServiceInterface $advantageService)
    {
        $this->advantageServices[] = $advantageService;
    }

    /**
     * @param AdvantageType $type
     *
     * @return AdvantageServiceInterface
     *
     * @throws InternalErrorException
     *   When no service was found for the provided type.
     */
    protected function getAdvantageServiceForType(AdvantageType $type) {
        foreach ($this->advantageServices as $advantageService) {
            if ($type->sameValueAs($advantageService->getType())) {
                return $advantageService;
            }
        }
        throw new InternalErrorException();
    }

    /**
     * @param string $uitpasNumber
     * @param string $advantageIdentifier
     *
     * @return JsonResponse
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
     *
     * @return JsonResponse
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
