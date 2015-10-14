<?php

namespace CultuurNet\UiTPASBeheer\Counter\Member;

use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use CultuurNet\UiTPASBeheer\User\UserServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ValueObjects\StringLiteral\StringLiteral;

class MemberController
{
    /**
     * @var MemberServiceInterface
     */
    private $memberService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var AddMemberJsonDeserializer
     */
    private $addMemberJsonDeserializer;

    /**
     * @param MemberServiceInterface $memberService
     * @param UserServiceInterface $userService
     * @param AddMemberJsonDeserializer $addMemberJsonDeserializer
     */
    public function __construct(
        MemberServiceInterface $memberService,
        UserServiceInterface $userService,
        AddMemberJsonDeserializer $addMemberJsonDeserializer
    ) {
        $this->memberService = $memberService;
        $this->userService = $userService;
        $this->addMemberJsonDeserializer = $addMemberJsonDeserializer;
    }

    /**
     * @return JsonResponse
     */
    public function all()
    {
        $all = $this->memberService->all();

        return (new JsonResponse())
            ->setData($all)
            ->setPrivate();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $add = $this->addMemberJsonDeserializer->deserialize(
            new StringLiteral(
                $request->getContent()
            )
        );

        $cfUser = $this->userService->getUserByEmail(
            $add->getEmailAddress()
        );

        $this->memberService->add(
            new Uid($cfUser->id)
        );

        return (new JsonResponse())
            ->setData($cfUser->id)
            ->setPrivate();
    }

    /**
     * @param string $uid
     * @return Response
     */
    public function remove($uid)
    {
        $this->memberService->remove(
            new Uid($uid)
        );

        return new Response();
    }
}
