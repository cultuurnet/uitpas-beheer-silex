<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultureFeed_Uitpas_Passholder_CardSystemPreferences;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;

class KansenStatuutService extends CounterAwareUitpasService implements KansenStatuutServiceInterface
{
    /**
     * @inheritdoc
     */
    public function renew(
        UiTPASNumber $uiTPASNumber,
        CardSystemId $cardSystemId,
        Date $newEndDate
    ) {
        $uitpasService = $this->getUitpasService();

        // Fetch the pass holder to get its internal ID which we need
        // to make the update request.
        $passHolder = $uitpasService->getPassholderByUitpasNumber(
            $uiTPASNumber->toNative(),
            $this->getCounterConsumerKey()
        );

        $cardSystemPreferences = new CultureFeed_Uitpas_Passholder_CardSystemPreferences();
        $cardSystemPreferences->id = $passHolder->uitIdUser->id;
        $cardSystemPreferences->balieConsumerKey = $this->getCounterConsumerKey();
        $cardSystemPreferences->cardSystemId = $cardSystemId->toNative();

        $cardSystemPreferences->kansenStatuutEndDate = $newEndDate
            ->toNativeDateTime()
            ->getTimestamp();

        try {
            $this->getUitpasService()->updatePassholderCardSystemPreferences(
                $cardSystemPreferences
            );
        } catch (\CultureFeed_Exception $e) {
            throw CompleteResponseException::fromCultureFeedException($e);
        }
    }
}
