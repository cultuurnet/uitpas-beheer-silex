<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Advantage\CashIn\CashIn;
use CultuurNet\UiTPASBeheer\Advantage\CashIn\CashInContentType;
use CultuurNet\UiTPASBeheer\Exception\InternalErrorException;
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
    protected $cashInJsonDeserializer;

    /**
     * @param DeserializerInterface $cashInJsonDeserializer
     */
    public function __construct(DeserializerInterface $cashInJsonDeserializer)
    {
        $this->cashInJsonDeserializer = $cashInJsonDeserializer;
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
     * @return JsonResponse
     */
    public function getCashable($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $advantages = array();
        foreach ($this->advantageServices as $advantageService) {
            $advantages = array_merge(
                $advantages,
                $advantageService->getCashable($uitpasNumber)
            );
        }

        return JsonResponse::create()
            ->setData($advantages)
            ->setPrivate(true);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cashIn(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $content = new StringLiteral($request->getContent());
        $contentType = new CashInContentType($request->headers->get('Content-Type'));

        /* @var CashIn $cashIn */
        $cashIn = $this->cashInJsonDeserializer->deserialize($content);

        $advantageId = $cashIn->getId();
        $advantageType = $contentType->getAdvantageType();

        $service = $this->getAdvantageServiceForType($advantageType);

        $service->cashIn(
            $uitpasNumber,
            $advantageId
        );

        $advantage = $service->get(
            $uitpasNumber,
            $advantageId
        );

        return JsonResponse::create()
            ->setData($advantage)
            ->setPrivate(true);
    }
}
