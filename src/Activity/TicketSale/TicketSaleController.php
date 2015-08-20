<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\StringLiteral\StringLiteral;

class TicketSaleController
{
    /**
     * @var TicketSaleService
     */
    protected $service;

    /**
     * @var DeserializerInterface
     */
    protected $jsonDeserializer;

    public function __construct(
        TicketSaleService $service,
        DeserializerInterface $registrationJsonDeserializer
    ) {
        $this->service = $service;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
    }

    /**
     * @param Request $request
     * @param $uitpasNumber
     */
    public function register(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        $this->service->register($uitpasNumber, $registration);
    }
}
