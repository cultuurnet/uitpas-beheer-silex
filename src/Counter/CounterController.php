<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CounterController
{
    /**
     * @var CounterServiceInterface
     */
    protected $service;

    /**
     * @param CounterServiceInterface $service
     */
    public function __construct(CounterServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function getCounters()
    {
        return new JsonResponse($this->service->getCounters());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws CounterNotFoundException
     *   If the provided counter can not be found or is not available for the current user.
     */
    public function setActiveCounter(Request $request)
    {
        $id = $request->request->get('id');
        $this->service->setActiveCounterId($id);

        $counter = $this->service->getActiveCounter();
        return new JsonResponse($counter);
    }

    /**
     * @return JsonResponse
     *
     * @throws CounterNotSetException
     *   If no active counter is set.
     */
    public function getActiveCounter()
    {
        return new JsonResponse($this->service->getActiveCounter());
    }
}
