<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HelpController
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(
        StorageInterface $storage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->storage = $storage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return JsonResponse
     */
    public function get()
    {
        $text = $this->storage->load();

        return $this->createResponse($text);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if (!$this->canUpdate()) {
            throw new AccessDeniedHttpException();
        }

        // @todo Deserialize text from request body.
        $text = new Text('foobar');

        $this->storage->save($text);

        return $this->createResponse($text);
    }

    /**
     * @param Text $text
     * @return JsonResponse
     */
    private function createResponse(Text $text)
    {
        return new JsonResponse(
            [
                'text' => $text->toNative(),
                'canUpdate' => $this->canUpdate(),
            ]
        );
    }

    /**
     * @return bool
     */
    private function canUpdate()
    {
        return $this->authorizationChecker->isGranted('ROLE_HELP_EDIT');
    }
}
