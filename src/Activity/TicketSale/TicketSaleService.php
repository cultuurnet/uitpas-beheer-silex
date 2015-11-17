<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\RegisteredTicketSale;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration\Registration;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolderNotFoundException;
use CultuurNet\UiTPASBeheer\PassHolder\PassHolderServiceInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class TicketSaleService extends CounterAwareUitpasService implements TicketSaleServiceInterface
{
    /**
     * @var PassHolderServiceInterface
     */
    protected $passHolderService;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param PassHolderServiceInterface $passHolderService
     */
    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        PassHolderServiceInterface $passHolderService
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);
        $this->passHolderService = $passHolderService;
    }

    /**
     * @param \ValueObjects\StringLiteral\StringLiteral $ticketId
     * @return boolean
     * @throws CompleteResponseException
     */
    public function cancel(StringLiteral $ticketId)
    {
        $ticketCancellation = $this->getUitpasService()->cancelTicketSaleById(
            $ticketId->toNative(),
            $this->getCounterConsumerKey()
        );

        return $ticketCancellation;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Registration $registration
     *
     * @return RegisteredTicketSale
     *
     * @throws CompleteResponseException
     *   When a CultureFeed_Exception was caught.
     */
    public function register(UiTPASNumber $uitpasNumber, Registration $registration)
    {
        $tariffId = $registration->getTariffId();

        if (!is_null($tariffId)) {
            $tariffId = $tariffId->toNative();
        }

        $amount = $registration->getAmount();
        if ($amount) {
            $amount = $amount->toNative();
        }

        try {
            $cfTicketSale = $this->getUitpasService()->registerTicketSale(
                $uitpasNumber->toNative(),
                $registration->getActivityId()->toNative(),
                $this->getCounterConsumerKey(),
                $registration->getPriceClass()->toNative(),
                $tariffId,
                $amount
            );
        } catch (\CultureFeed_Exception $e) {
            throw CompleteResponseException::fromCultureFeedException($e);
        }

        return RegisteredTicketSale::fromCultureFeedTicketSale($cfTicketSale);
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return TicketSale[]
     *
     * @throws PassHolderNotFoundException
     *   When no UID was found for the provided uitpasNumber.
     */
    public function getByUiTPASNumber(UiTPASNumber $uitpasNumber)
    {
        $passHolder = $this->passHolderService->getByUitpasNumber($uitpasNumber);

        if (is_null($passHolder) || is_null($passHolder->getUid())) {
            throw new PassHolderNotFoundException();
        }

        $query = new \CultureFeed_Uitpas_Event_Query_SearchTicketSalesOptions();
        $query->uid = $passHolder->getUid()->toNative();
        $query->balieConsumerKey = $this->getCounterConsumerKey();

        $cfResults = $this->getUitpasService()->searchTicketSales($query);

        $ticketSales = array_map(
            function ($cfTicketSale) {
                return TicketSale::fromCultureFeedTicketSale($cfTicketSale);
            },
            $cfResults->objects
        );

        return $ticketSales;
    }
}
