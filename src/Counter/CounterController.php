<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\StringLiteral\StringLiteral;

class CounterController
{
    /**
     * @var CounterServiceInterface
     */
    protected $service;

    /**
     * @var CounterIDJsonDeserializer
     */
    protected $counterIDJsonDeserializer;

    /**
     * @param CounterServiceInterface $service
     */
    public function __construct(
        CounterServiceInterface $service,
        CounterIDJsonDeserializer $counterIDJsonDeserializer
    ) {
        $this->service = $service;
        $this->counterIDJsonDeserializer = $counterIDJsonDeserializer;
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
        $id = $this->counterIDJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );
        $counter = $this->service->getCounter($id);

        $this->service->setActiveCounter($counter);

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
