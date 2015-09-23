<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use CultuurNet\UiTPASBeheer\Activity\Activity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\StringLiteral\StringLiteral;

class CheckInDeviceController
{
    /**
     * @var CheckInDeviceServiceInterface
     */
    private $checkInDevices;

    /**
     * @param CheckInDeviceServiceInterface $checkInDevices
     */
    public function __construct(CheckInDeviceServiceInterface $checkInDevices)
    {
        $this->checkInDevices = $checkInDevices;
    }

    public function all()
    {
        return JsonResponse::create($this->checkInDevices->all());
    }

    /**
     * @return JsonResponse
     */
    public function availableActivities()
    {
        return JsonResponse::create(
            array_map(
                function (Activity $activity) {
                    return [
                        'id' => $activity->getId()->toNative(),
                        'title' => $activity->getTitle()->toNative(),
                    ];
                },
                $this->checkInDevices->availableActivities()
            )
        );
    }

    public function connectDeviceToActivity(Request $request, $checkInDeviceId)
    {
        $activityId = (new ConnectToActivityJSONDeserializer())->deserialize(
            new StringLiteral($request->getContent())
        );
        $checkInDeviceId = new StringLiteral($checkInDeviceId);

        return JsonResponse::create(
            $this->checkInDevices->connectDeviceToActivity(
                $checkInDeviceId,
                $activityId
            )
        );
    }
}
