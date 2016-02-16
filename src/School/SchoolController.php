<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use Symfony\Component\HttpFoundation\JsonResponse;

class SchoolController
{
    /**
     * @var SchoolServiceInterface
     */
    private $schoolService;

    public function __construct(SchoolServiceInterface $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    /**
     * @return JsonResponse
     */
    public function getSchools()
    {
        $response = JsonResponse::create($this->schoolService->getSchools());

        return $response;
    }
}
