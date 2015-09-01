<?php

namespace CultuurNet\UiTPASBeheer\Membership;

use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Association;
use CultureFeed_Uitpas_Passholder_Membership;
use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\LegacyPassHolderServiceInterface;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use CultuurNet\UiTPASBeheer\Membership\Association\UnregisteredAssociationFilter;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications\HasAtLeastOneExpiredKansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolderNotFoundException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ValueObjects\StringLiteral\StringLiteral;

class MembershipController
{
    /**
     * @var MembershipService
     */
    private $membershipService;

    /**
     * @var LegacyPassHolderServiceInterface
     */
    private $legacyPassHolderService;

    /**
     * @var DeserializerInterface
     */
    private $registrationJsonDeserializer;

    /**
     * @param MembershipServiceInterface $membershipService
     * @param DeserializerInterface $registrationJsonDeserializer
     * @param LegacyPassHolderServiceInterface $legacyPassHolderService
     */
    public function __construct(
        MembershipServiceInterface $membershipService,
        DeserializerInterface $registrationJsonDeserializer,
        LegacyPassHolderServiceInterface $legacyPassHolderService
    ) {
        $this->membershipService = $membershipService;
        $this->legacyPassHolderService = $legacyPassHolderService;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
    }

    /**
     * @param string $uitpasNumber
     * @return Response
     */
    public function listing($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $passHolder = $this->legacyPassHolderService->getByUiTPASNumber($uitpasNumber);

        $all = $this->membershipService->getAssociations();

        $unregisteredFilter = new UnregisteredAssociationFilter($passHolder);
        $unregistered = $unregisteredFilter->filter($all);

        return JsonResponse::create(
            [
                'passholder' => $passHolder,
                'atLeastOneKansenstatuutExpired' => HasAtLeastOneExpiredKansenStatuut::isSatisfiedBy($passHolder),
                'otherAssociations' => array_values($unregistered->jsonSerialize()),
                'allAssociations' => $all->jsonSerialize(),
            ]
        )->setPrivate();
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     * @return Response
     */
    public function register(Request $request, $uitpasNumber)
    {
        $uid = $this->getUidForUiTPASNumber(
            new UiTPASNumber($uitpasNumber)
        );

        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        $result = $this->membershipService->register(
            $uid,
            $registration
        );

        return JsonResponse::create($result)
            ->setPrivate();
    }

    /**
     * @param string $uitpasNumber
     * @param string $associationId
     * @return Response
     */
    public function stop($uitpasNumber, $associationId)
    {
        $uid = $this->getUidForUiTPASNumber(
            new UiTPASNumber($uitpasNumber)
        );

        $associationId = new AssociationId($associationId);

        $result = $this->membershipService->stop($uid, $associationId);

        return JsonResponse::create($result)
            ->setPrivate();
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return StringLiteral
     *
     * @throws PassHolderNotFoundException
     *   When a passholder could not be found for the given uitpas number.
     */
    private function getUidForUiTPASNumber(UiTPASNumber $uitpasNumber)
    {
        $passHolder = $this->legacyPassHolderService->getByUiTPASNumber($uitpasNumber);

        if (is_null($passHolder)) {
            throw new PassHolderNotFoundException();
        }

        return new StringLiteral((string) $passHolder->uitIdUser->id);
    }
}
