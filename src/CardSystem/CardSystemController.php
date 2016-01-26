<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\UnknownEnumParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\PassHolder\VoucherNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class CardSystemController
{
    /**
     * @var CardSystemServiceInterface
     */
    protected $cardSystemService;

    public function __construct(CardSystemServiceInterface $cardSystemService)
    {
        $this->cardSystemService = $cardSystemService;
    }

    /**
     * @param Request $request
     * @param $cardSystemId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws IncorrectParameterValueException
     * @throws UnknownEnumParameterValueException
     * @throws UnknownParameterException
     */
    public function getPrice(Request $request, $cardSystemId)
    {
        $cardSystemId = new CardSystemId($cardSystemId);

        $inquiry = new Inquiry(
            $cardSystemId
        );

        foreach ($request->query->all() as $parameter => $value) {
            switch ($parameter) {

                case 'date_of_birth':
                    $datetime = \DateTime::createFromFormat('Y-m-d', $value);
                    if (!$datetime) {
                        throw new IncorrectParameterValueException('date_of_birth');
                    }

                    $inquiry = $inquiry->withDateOfBirth(
                        Date::fromNativeDateTime($datetime)
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

        $price = $this->cardSystemService->getPrice($inquiry);

        return JsonResponse::create($price)
            ->setPrivate();
    }
}
