<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\StringLiteral\StringLiteral;

class TicketSaleController
{
    /**
     * @var TicketSaleServiceInterface
     */
    protected $service;

    /**
     * @var DeserializerInterface
     */
    protected $jsonDeserializer;

    /**
     * @param TicketSaleServiceInterface $service
     * @param DeserializerInterface $registrationJsonDeserializer
     */
    public function __construct(
        TicketSaleServiceInterface $service,
        DeserializerInterface $registrationJsonDeserializer
    ) {
        $this->service = $service;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
    }

    /**
     * @param Request $request
     * @param $uitpasNumber
     *
     * @return Response
     */
    public function register(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        $ticketSale = $this->service->register($uitpasNumber, $registration);

        return JsonResponse::create($ticketSale)
            ->setPrivate();
    }

    /**
     * @param $uitpasNumber
     *
     * @return Response
     */
    public function getByUiTPASNumber($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $ticketSales = $this->service->getByUiTPASNumber($uitpasNumber);

        return JsonResponse::create($ticketSales)
            ->setPrivate();
    }
}
