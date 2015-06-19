<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PassHolderController
{
    /**
     * @param PassHolderService $passHolderService
     */
    protected $passHolderService;

    public function __construct(PassHolderService $passHolderService)
    {
        $this->passHolderService = $passHolderService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function find(Request $request)
    {
        $identificationNumber = $request->request->get('identification');

        $passholder = $this->passHolderService->getPassHolderByIdentificationNumber($identificationNumber);

        if (is_null($passholder)) {
            throw new PassHolderNotFoundException();
        }

        return JsonResponse::create()
            ->setData($passholder)
            ->setPrivate();
    }
}
