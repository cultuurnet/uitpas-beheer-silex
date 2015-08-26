<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderController
{
    /**
     * @var PassHolderServiceInterface
     */
    protected $passHolderService;

    /**
     * @var DeserializerInterface
     */
    protected $passHolderJsonDeserializer;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $registrationJsonDeserializer;

    /**
     * @param PassHolderServiceInterface $passHolderService
     * @param DeserializerInterface $passHolderJsonDeserializer
     * @param DeserializerInterface $registrationJsonDeserializer
     */
    public function __construct(
        PassHolderServiceInterface $passHolderService,
        DeserializerInterface $passHolderJsonDeserializer,
        DeserializerInterface $registrationJsonDeserializer
    ) {
        $this->passHolderService = $passHolderService;
        $this->passHolderJsonDeserializer = $passHolderJsonDeserializer;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
    }

    /**
     * @param string $uitpasNumber
     * @return JsonResponse

     * @throws PassHolderNotFoundException
     *   When no passholder was found for the provided uitpas number.
     */
    public function getByUitpasNumber($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $passholder = $this->passHolderService->getByUitpasNumber($uitpasNumber);

        if (is_null($passholder)) {
            throw new PassHolderNotFoundException();
        }

        return JsonResponse::create()
            ->setData($passholder)
            ->setPrivate();
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return JsonResponse
     *
     * @throws ReadableCodeResponseException
     *   When a CultureFeed_Exception is encountered.
     */
    public function update(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $passHolder = $this->passHolderJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        try {
            $this->passHolderService->update($uitpasNumber, $passHolder);
        } catch (\CultureFeed_Exception $exception) {
            throw ReadableCodeResponseException::fromCultureFeedException($exception);
        }

        return $this->getByUitpasNumber($uitpasNumber->toNative());
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return JsonResponse
     *
     * @throws ReadableCodeResponseException
     *   When a CultureFeed_Exception is encountered.
     */
    public function register(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        try {
            $this->passHolderService->register(
                $uitpasNumber,
                $registration->getPassholder(),
                $registration->getVoucherNumber(),
                $registration->getKansenstatuut()
            );
        } catch (\CultureFeed_Exception $exception) {
            throw ReadableCodeResponseException::fromCultureFeedException($exception);
        }

        // Return the registered passholder.
        $passholder = $this->passHolderService->getByUitpasNumber($uitpasNumber);

        $response = JsonResponse::create()
            ->setData($passholder)
            ->setPrivate();

        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function getPrices()
    {
        $offers = $this->passHolderService->getPrices();

        return JsonResponse::create()
            ->setData($offers)
            ->setPrivate();
    }
}
