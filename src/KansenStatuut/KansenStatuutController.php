<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ValueObjects\StringLiteral\StringLiteral;

class KansenStatuutController
{
    /**
     * @var KansenStatuutServiceInterface
     */
    private $kansenStatuutService;

    /**
     * @var KansenStatuutEndDateJSONDeserializer
     */
    private $kansenStatuutEndDateJSONDeserializer;

    /**
     * @param KansenStatuutServiceInterface $kansenStatuutService
     * @param DeserializerInterface $kansenStatuutEndDateJSONDeserializer
     */
    public function __construct(
        KansenStatuutServiceInterface $kansenStatuutService,
        DeserializerInterface $kansenStatuutEndDateJSONDeserializer
    ) {
        $this->kansenStatuutService = $kansenStatuutService;
        $this->kansenStatuutEndDateJSONDeserializer = $kansenStatuutEndDateJSONDeserializer;
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     * @param string $cardSystemId
     * @return Response
     */
    public function renew(Request $request, $uitpasNumber, $cardSystemId)
    {
        $newEndDate = $this->kansenStatuutEndDateJSONDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        $this->kansenStatuutService->renew(
            new UiTPASNumber($uitpasNumber),
            new CardSystemId($cardSystemId),
            $newEndDate
        );

        return new Response('', Response::HTTP_OK);
    }
}
