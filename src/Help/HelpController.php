<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Help;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HelpController
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(
        StorageInterface $storage
    ) {
        $this->storage = $storage;
    }

    /**
     * @return JsonResponse
     */
    public function get()
    {
        $text = $this->storage->load();

        return new JsonResponse(
            [
                'text' => $text->toNative(),
                // @todo Put list of editor ids here.
                'editors' => []
            ]
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $text = new Text('foobar');

        $this->storage->save($text);

        return new JsonResponse(
            [
                'text' => $text->toNative(),
                // @todo Put list of editor ids here.
                'editors' => []
            ]
        );
    }
}
