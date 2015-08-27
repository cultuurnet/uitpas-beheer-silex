<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\Exception\MissingParameterException;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\PurchaseReason;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\DateTime;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASController
{
    /**
     * @var UiTPASServiceInterface
     */
    protected $uitpasService;

    /**
     * @param UiTPASServiceInterface $uitpasService
     */
    public function __construct(UiTPASServiceInterface $uitpasService)
    {
        $this->uitpasService = $uitpasService;
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return Price
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
                    $inquiry->withDateOfBirth(
                        DateTime::fromNativeDateTime(
                            \DateTime::createFromFormat('Y-m-d', $value)
                        )
                    );
                    break;

                case 'postal_code':
                    $inquiry->withPostalCode(
                        new StringLiteral($value)
                    );
                    break;

                case 'voucher_number':
                    $inquiry->withVoucherNumber(
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
