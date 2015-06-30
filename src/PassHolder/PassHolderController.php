<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

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
}
