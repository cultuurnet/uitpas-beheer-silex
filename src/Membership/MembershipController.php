<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Membership;

use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Association;
use CultureFeed_Uitpas_Passholder_Membership;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Legacy\LegacyPassHolderServiceInterface;
use CultuurNet\UiTPASBeheer\Membership\Specifications\HasAtLeastOneExpiredKansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MembershipController
{
    /**
     * @var CultureFeed_Uitpas
     */
    private $uitpas;

    /**
     * @var CounterConsumerKey
     */
    private $counterConsumerKey;

    /**
     * @var LegacyPassHolderServiceInterface
     */
    private $legacyPassHolderService;

    /**
     * @param LegacyPassHolderServiceInterface $legacyPassHolderService
     * @param CultureFeed_Uitpas $uitpas
     * @param CounterConsumerKey $consumerKey
     */
    public function __construct(
        LegacyPassHolderServiceInterface $legacyPassHolderService,
        CultureFeed_Uitpas $uitpas,
        CounterConsumerKey $consumerKey
    ) {
        $this->legacyPassHolderService = $legacyPassHolderService;
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

        $associations = $this->uitpas->getAssociations(
            $this->counterConsumerKey->toNative()
        );

        // Put associations in a list, keyed by their ID.
        $associationsMap = [];
        /** @var CultureFeed_Uitpas_Association $association */
        foreach ($associations->objects as $association) {
            $associationsMap[$association->id] = $association;
        }

        $allAssociations = $associationsMap;

        // Remove associations that have a corresponding membership for this
        // passholder, from the keyed list.
        foreach ($passHolder->memberships as $membership) {
            if (isset($associationsMap[$membership->association->id])) {
                unset($associationsMap[$membership->association->id]);
            }
        }

        return JsonResponse::create(
            [
                'passholder' => $passHolder,
                'atLeastOneKansenstatuutExpired' => HasAtLeastOneExpiredKansenStatuut::isSatisfiedBy($passHolder),
                'otherAssociations' => array_values($associationsMap),
                'allAssociations' => $allAssociations,
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
        $data = json_decode($request->getContent());

        $passHolder = $this->legacyPassHolderService->getByUiTPASNumber($uitpasNumber);

        $membership = new CultureFeed_Uitpas_Passholder_Membership();
        $membership->associationId = $data->associationId;
        $membership->balieConsumerKey = $this->counterConsumerKey->toNative();

        $membership->uid = $passHolder->uitIdUser->id;

        if (isset($data->endDate)) {
            $membership->endDate = (int) DateTime::createFromFormat(
                'Y-m-d',
                $data->endDate
            )->format('U');
        }

        $result = $this->uitpas->createMembershipForPassholder($membership);

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
