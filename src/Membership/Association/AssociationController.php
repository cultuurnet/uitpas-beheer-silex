<?php

namespace CultuurNet\UiTPASBeheer\Membership\Association;

use Symfony\Component\HttpFoundation\JsonResponse;

class AssociationController
{
    /**
     * @var AssociationServiceInterface
     */
    protected $service;

    /**
     * @param AssociationServiceInterface $service
     */
    public function __construct(AssociationServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function getAssociations()
    {
        return (new JsonResponse())
            ->setData($this->service->getAssociationsByPermission(Permission::REGISTER()))
            ->setPrivate();
    }
}
