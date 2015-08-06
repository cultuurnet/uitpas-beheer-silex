<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IdentityController
{
    /**
     * @var IdentityServiceInterface
     */
    protected $identityService;

    /**
     * @param IdentityServiceInterface $identityService
     */
    public function __construct(IdentityServiceInterface $identityService)
    {
        $this->identityService = $identityService;
    }

    /**
     * @param string $identificationNumber
     * @return JsonResponse
     *
     * @throws IdentityNotFoundException
     *   When no identity was found for the provided identification number.
     */
    public function get($identificationNumber)
    {
        $identity = $this->identityService->get($identificationNumber);

        if (is_null($identity)) {
            throw new IdentityNotFoundException();
        }

        return JsonResponse::create()
            ->setData($identity)
            ->setPrivate();
    }
}
