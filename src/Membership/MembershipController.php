<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Membership;

use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Association;
use CultureFeed_Uitpas_Passholder_Membership;
use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\LegacyPassHolderServiceInterface;
use CultuurNet\UiTPASBeheer\Membership\Association\UnregisteredAssociationFilter;
use CultuurNet\UiTPASBeheer\Legacy\PassHolder\Specifications\HasAtLeastOneExpiredKansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ValueObjects\StringLiteral\StringLiteral;

class MembershipController
{
    /**
     * @var CultureFeed_Uitpas
     */
    private $uitpas;

    /**
     * @var MembershipService
     */
    private $membershipService;

    /**
     * @var CounterConsumerKey
     */
    private $counterConsumerKey;

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
     * @param LegacyPassHolderServiceInterface $legacyPassHolderService
     * @param DeserializerInterface $registrationJsonDeserializer
     * @param CultureFeed_Uitpas $uitpas
     * @param CounterConsumerKey $consumerKey
     */
    public function __construct(
        MembershipServiceInterface $membershipService,
        LegacyPassHolderServiceInterface $legacyPassHolderService,
        DeserializerInterface $registrationJsonDeserializer,
        CultureFeed_Uitpas $uitpas,
        CounterConsumerKey $consumerKey
    ) {
        $this->membershipService = $membershipService;
        $this->legacyPassHolderService = $legacyPassHolderService;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
        $this->uitpas = $uitpas;
        $this->counterConsumerKey = $consumerKey;
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
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        $passHolder = $this->legacyPassHolderService->getByUiTPASNumber($uitpasNumber);
        $uid = new StringLiteral((string) $passHolder->uitIdUser->id);

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
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $passHolder = $this->legacyPassHolderService->getByUiTPASNumber($uitpasNumber);

        $result = $this->uitpas->deleteMembership(
            $passHolder->uitIdUser->id,
            $associationId,
            $this->counterConsumerKey->toNative()
        );

        return JsonResponse::create($result)
            ->setPrivate();
    }
}
