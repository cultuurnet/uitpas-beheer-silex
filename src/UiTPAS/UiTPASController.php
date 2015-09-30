<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\Exception\MissingParameterException;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\RegistrationJsonDeserializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASController
{
    /**
     * @var UiTPASServiceInterface
     */
    protected $uitpasService;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $registrationJsonDeserializer;

    /**
     * @param UiTPASServiceInterface $uitpasService
     * @param RegistrationJsonDeserializer $registrationJsonDeserializer
     */
    public function __construct(
        UiTPASServiceInterface $uitpasService,
        RegistrationJsonDeserializer $registrationJsonDeserializer
    ) {
        $this->uitpasService = $uitpasService;
        $this->registrationJsonDeserializer = $registrationJsonDeserializer;
    }

    /**
     * @param string $uitpasNumber
     * @return Response
     */
    public function block($uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $this->uitpasService->block($uitpasNumber);
        $uitpas = $this->uitpasService->get($uitpasNumber);

        return JsonResponse::create($uitpas)
            ->setPrivate(true);
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return Response
     */
    public function register(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $registration = $this->registrationJsonDeserializer->deserialize(
            new StringLiteral($request->getContent())
        );

        $this->uitpasService->register($uitpasNumber, $registration);
        $uitpas = $this->uitpasService->get($uitpasNumber);

        return JsonResponse::create($uitpas)
            ->setPrivate(true);
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return Response
     *
     * @throws MissingParameterException
     *   When a required query parameter is missing.
     *
     * @throws UnknownParameterException
     *   When an unknown query parameter was provided.
     */
    public function getPrice(Request $request, $uitpasNumber)
    {
        if (is_null($request->query->get('reason'))) {
            throw new MissingParameterException('reason');
        }

        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $reason = PurchaseReason::fromNative($request->query->get('reason'));

        $inquiry = new Inquiry(
            $uitpasNumber,
            $reason
        );

        foreach ($request->query->all() as $parameter => $value) {
            switch ($parameter) {
                case 'reason':
                    // Handled earlier because it's a required parameter.
                    break;

                case 'date_of_birth':
                    $inquiry = $inquiry->withDateOfBirth(
                        Date::fromNativeDateTime(
                            \DateTime::createFromFormat('Y-m-d', $value)
                        )
                    );
                    break;

                case 'postal_code':
                    $inquiry = $inquiry->withPostalCode(
                        new StringLiteral($value)
                    );
                    break;

                case 'voucher_number':
                    $inquiry = $inquiry->withVoucherNumber(
                        new VoucherNumber($value)
                    );
                    break;

                default:
                    throw new UnknownParameterException($parameter);
                    break;
            }
        }

        $price = $this->uitpasService->getPrice($inquiry);

        return JsonResponse::create($price)
            ->setPrivate(true);
    }
}
