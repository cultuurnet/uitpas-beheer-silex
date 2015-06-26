<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PassHolderController
{
    /**
     * @param PassHolderServiceInterface $passHolderService
     */
    protected $passHolderService;

    public function __construct(PassHolderServiceInterface $passHolderService)
    {
        $this->passHolderService = $passHolderService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getByIdentificationNumber(Request $request)
    {
        $identificationNumber = $request->query->get('identification');
        $passholder = $this->passHolderService->getByIdentificationNumber($identificationNumber);

        if (is_null($passholder)) {
            throw new PassHolderNotFoundException();
        }

        return JsonResponse::create()
            ->setData($passholder)
            ->setPrivate();
    }

    /**
     * @param string $uitpasNumber
     * @return JsonResponse
     */
    public function getByUitpasNumber($uitpasNumber)
    {
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
        $passHolder = new \CultureFeed_Uitpas_Passholder();
        $passHolder->uitpasNumber = $uitpasNumber;

        $properties = $request->request->all();
        foreach ($properties as $property => $value) {
            $passHolder->{$property} = $value;
        }

        try {
            $this->passHolderService->update($passHolder);
        } catch (\CultureFeed_Exception $exception) {
            throw ReadableCodeResponseException::fromCultureFeedException($exception);
        }

        return $this->getByUitpasNumber($uitpasNumber);
    }
}
