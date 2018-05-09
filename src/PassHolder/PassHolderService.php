<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\Identity\Identity;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\OptInPreferences;
use CultuurNet\UiTPASBeheer\School\SchoolConsumerKey;
use CultuurNet\UiTPASBeheer\PassHolder\Search\PagedResultSet;
use CultuurNet\UiTPASBeheer\PassHolder\Search\QueryBuilderInterface;
use CultuurNet\UiTPASBeheer\UiTPAS\Filter\UiTPASSpecificationFilter;
use CultuurNet\UiTPASBeheer\UiTPAS\Specifications\HasAnyOfNumbers;
use CultuurNet\UiTPASBeheer\UiTPAS\Specifications\UsableByCounter;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use ValueObjects\Identity\UUID;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderService extends CounterAwareUitpasService implements PassHolderServiceInterface
{
    /**
     * @var \CultureFeed_Uitpas_Counter_Employee
     */
    private $counter;

    /**
     * @param \CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param \CultureFeed_Uitpas_Counter_Employee $counter
     */
    public function __construct(
        \CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        \CultureFeed_Uitpas_Counter_Employee $counter
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);
        $this->counter = $counter;
    }

    /**
     * Returns a PagedResultSet with Identities instead of PassHolders, but
     * it's not in the Identity namespace because we're only searching for
     * passholder identities (and not group passes for example).
     *
     * @param QueryBuilderInterface $query
     * @return PagedResultSet
     */
    public function search(QueryBuilderInterface $query)
    {
        $options = $query->build();
        $options->balieConsumerKey = $this->getCounterConsumerKey();

        $result = $this->getUitpasService()->searchPassholders($options);

        // Create a filter that helps determine what uitpas to use as primary
        // uitpas in the identity.
        $searchedNumbers = $query->getUiTPASNumbers();
        if (!is_null($searchedNumbers) && $searchedNumbers->length() > 0) {
            $specification = new HasAnyOfNumbers($searchedNumbers);
        } else {
            $specification = new UsableByCounter($this->counter);
        }
        $uitpasCollectionFilter = new UiTPASSpecificationFilter($specification);

        $identities = array_map(
            function (\CultureFeed_Uitpas_Passholder $passHolder) use ($uitpasCollectionFilter) {
                $passHolder = PassHolder::fromCultureFeedPassHolder($passHolder);

                $uitpasCollection = $passHolder->getUiTPASCollection();
                if (is_null($uitpasCollection) || $uitpasCollection->length() === 0) {
                    throw new \LogicException('PassHolder returned by search has not a single uitpas.');
                }

                return Identity::fromPassHolderWithUitpasCollection(
                    $passHolder,
                    $uitpasCollectionFilter
                );
            },
            $result->objects
        );

        $pagedResultSet = new PagedResultSet(
            new Integer((int) $result->total),
            $identities
        );

        $invalidUitpasNumbers = $result->invalidUitpasNumbers ? $result->invalidUitpasNumbers : array();
        $invalidUitpasNumbers = array_map(
            function ($uitpasNumber) {
                return UiTPASNumber::fromNative($uitpasNumber);
            },
            $invalidUitpasNumbers
        );

        if (!empty($invalidUitpasNumbers)) {
            $invalidUitpasNumbers = UiTPASNumberCollection::fromArray($invalidUitpasNumbers);
            $pagedResultSet = $pagedResultSet->withInvalidUiTPASNumbers($invalidUitpasNumbers);
        }

        return $pagedResultSet;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return PassHolder|null
     */
    public function getByUitpasNumber(UiTPASNumber $uitpasNumber)
    {
        try {
            $cfPassHolder = $this
                    ->getUitpasService()
                    ->getPassholderByUitpasNumber(
                        $uitpasNumber->toNative(),
                        $this->getCounterConsumerKey()
                    );
            return PassHolder::fromCultureFeedPassHolder($cfPassHolder);
        } catch (\CultureFeed_Exception $exception) {
            return null;
        }
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param PassHolder $passHolder
     */
    public function update(
        UiTPASNumber $uitpasNumber,
        PassHolder $passHolder
    ) {
        $existingPassHolder = $this->getByUitpasNumber($uitpasNumber);

        // Update regular data
        $cfPassHolder = $this->createCultureFeedPassholder($passHolder);
        $cfPassHolder->uitpasNumber = $uitpasNumber->toNative();

        $this
            ->getUitpasService()
            ->updatePassholder(
                $cfPassHolder,
                $this->getCounterConsumerKey()
            );

        // Update opt-in preferences.
        $this->updateOptInPreferences($existingPassHolder->getUid(), $passHolder->getOptInPreferences(), $this->getCounterConsumerKey());

        // Upload picture.
        $picture = $passHolder->getPicture();

        if ($picture) {
            $passHolderId = $existingPassHolder->getUid();

            $this->uploadPicture($passHolderId, $picture);
        }
    }

    /**
     * @param StringLiteral $passHolderUUID
     * @param OptInPreferences $optInPreferences
     */
    private function updateOptInPreferences(
        StringLiteral $passHolderUUID,
        OptInPreferences $optInPreferences,
        $consumer_key_counter = NULL
    ) {

        $cfOptInPreferences = $this->createCultureFeedOptInPreferences($optInPreferences);

        $this
            ->getUitpasService()
            ->updatePassholderOptInPreferences(
                $passHolderUUID,
                $cfOptInPreferences,
                $consumer_key_counter
            );
    }

    /**
     * @param StringLiteral $passHolderUUID
     * @param StringLiteral $picture
     */
    private function uploadPicture(
        StringLiteral $passHolderUUID,
        StringLiteral $picture
    ) {
        $this->getUitpasService()
            ->uploadPicture(
                $passHolderUUID->toNative(),
                base64_decode($picture->toNative()),
                $this->getCounterConsumerKey()
            );
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param CardSystemUpgrade $cardSystemUpgrade
     */
    public function upgradeCardSystems(UiTPASNumber $uitpasNumber, CardSystemUpgrade $cardSystemUpgrade)
    {
        $registration = new \CultureFeed_Uitpas_Passholder_Query_RegisterInCardSystemOptions();
        $registration->balieConsumerKey = $this->getCounterConsumerKey();

        $cardSystemId = $cardSystemUpgrade->getCardSystemId();
        if ($cardSystemId) {
            $registration->cardSystemId = $cardSystemId->toNative();
        } else {
            $registration->uitpasNumber = $cardSystemUpgrade
                ->getNewUiTPAS()
                ->toNative();

            $registration->kansenStatuut = false;
            $kansenStatuut = $cardSystemUpgrade->getKansenStatuut();
            if ($kansenStatuut) {
                $registration->kansenStatuut = true;
                $registration->kansenStatuutEndDate = $kansenStatuut
                    ->getEndDate()
                    ->toNativeDateTime()
                    ->getTimestamp();
            }
        }

        $passHolderId = $this->getByUitpasNumber($uitpasNumber)->getUid();

        $this->getUitpasService()->registerPassholderInCardSystem(
            $passHolderId->toNative(),
            $registration
        );
    }


    /**
     * {@inheritdoc}
     **/
    public function register(
        UiTPASNumber $uitpasNumber,
        Passholder $passHolder,
        VoucherNumber $voucherNumber = null,
        KansenStatuut $kansenStatuut = null,
        SchoolConsumerKey $schoolConsumerKey = null,
        $legalTermsPaper = false,
        $legalTermsDigital = false,
        $parentalConsent = false
    ) {
        $existingPassHolder = $this->getByUitpasNumber($uitpasNumber);

        if ($existingPassHolder) {
            throw new UiTPASNumberAlreadyUsedException();
        };

        $cfPassHolder = $this->createCultureFeedPassholder($passHolder);
        $cfPassHolder->uitpasNumber = $uitpasNumber->toNative();

        if ($voucherNumber) {
            $cfPassHolder->voucherNumber = $voucherNumber->toNative();
        }

        if ($uitpasNumber->hasKansenStatuut()) {
            if (is_null($kansenStatuut)) {
                throw new \InvalidArgumentException(
                    'The kansenStatuut argument should not be null for the provided UiTPASNumber.'
                );
            } else {
                $cfPassHolder->kansenStatuut = true;
                $cfPassHolder->kansenStatuutEndDate = $kansenStatuut
                    ->getEndDate()
                    ->toNativeDateTime()
                    ->getTimestamp();
                $cfPassHolder->moreInfo = (string) $kansenStatuut->getRemarks();
            }
        }

        // School consumer key.
        if (!is_null($schoolConsumerKey)) {
            $cfPassHolder->schoolConsumerKey = $schoolConsumerKey->toNative();
        }

        // Terms info.
        $cfPassHolder->legalTermsPaper = $legalTermsPaper;
        $cfPassHolder->legalTermsDigital = $legalTermsDigital;
        $cfPassHolder->parentalConsent = $parentalConsent;

        $UUIDString = $this->getUitpasService()->createPassholder(
            $cfPassHolder,
            $this->getCounterConsumerKey()
        );

        $UUID = UUID::fromNative($UUIDString);

        $picture = $passHolder->getPicture();

        if ($picture) {
            $this->uploadPicture($UUID, $picture);
        }

        return $UUID;
    }

    /**
     * @param PassHolder $passHolder
     *
     * @return \CultureFeed_Uitpas_Passholder
     */
    private function createCultureFeedPassholder(PassHolder $passHolder)
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();

        $cfPassHolder->firstName = $passHolder->getName()->getFirstName()->toNative();
        $cfPassHolder->name = $passHolder->getName()->getLastName()->toNative();
        if ($passHolder->getName()->getMiddleName()) {
            $cfPassHolder->secondName = $passHolder
                ->getName()
                ->getMiddleName()
                ->toNative();
        }

        if ($passHolder->getNationality()) {
            $cfPassHolder->nationality = $passHolder
                ->getNationality()
                ->toNative();
        }

        $birthInformation = $passHolder->getBirthInformation();

        if ($birthInformation->getPlace()) {
            $cfPassHolder->placeOfBirth = $birthInformation
                ->getPlace()
                ->toNative();
        }

        $cfPassHolder->dateOfBirth = $birthInformation
            ->getDate()
            ->toNativeDateTime()
            ->getTimestamp();

        if ($passHolder->getGender()) {
            $cfPassHolder->gender = $passHolder->getGender()->toNative();
        }

        $address = $passHolder->getAddress();

        if ($address->getStreet()) {
            $cfPassHolder->street = $address->getStreet()->toNative();
        }

        $cfPassHolder->city = $address->getCity()->toNative();
        $cfPassHolder->postalCode = $address->getPostalCode()->toNative();


        $contactInformation = $passHolder->getContactInformation();
        if ($contactInformation) {
            if ($contactInformation->getMobileNumber()) {
                $cfPassHolder->gsm = $contactInformation
                    ->getMobileNumber()
                    ->toNative();
            }

            if ($contactInformation->getTelephoneNumber()) {
                $cfPassHolder->telephone = $contactInformation
                    ->getTelephoneNumber()
                    ->toNative();
            }

            if ($contactInformation->getEmail()) {
                $cfPassHolder->email = $contactInformation
                    ->getEmail()
                    ->toNative();
            }
        }

        $privacyPreferences = $passHolder->getPrivacyPreferences();

        if ($privacyPreferences) {
            $cfPassHolder->emailPreference = $privacyPreferences
                ->getEmailPreference()
                ->toNative();
            $cfPassHolder->smsPreference = $privacyPreferences
                ->getSMSPreference()
                ->toNative();
        }

        if ($passHolder->getINSZNumber()) {
            $cfPassHolder->inszNumber = $passHolder
                ->getINSZNumber()
                ->toNative();
        }

        if ($passHolder->getRemarks()) {
            $cfPassHolder->moreInfo = $passHolder
                ->getRemarks()
                ->toNative();
        }

        $school = $passHolder->getSchool();
        if ($school) {
            $cfPassHolder->schoolConsumerKey = $school->getId()->toNative();
        }

        $optInPreferences = $passHolder->getOptInPreferences();

        if ($optInPreferences) {
            $cfPassHolder->optInServiceMails = $optInPreferences->hasOptInServiceMails();
            $cfPassHolder->optInMilestoneMails = $optInPreferences->hasOptInMilestoneMails();
            $cfPassHolder->optInInfoMails = $optInPreferences->hasOptInInfoMails();
            $cfPassHolder->optInSms = $optInPreferences->hasOptInSms();
            $cfPassHolder->optInPost = $optInPreferences->hasOptInPost();
        }

        $cfPassHolder->toPostDataKeepEmptySecondName();
        $cfPassHolder->toPostDataKeepEmptyEmail();
        $cfPassHolder->toPostDataKeepEmptyMoreInfo();
        $cfPassHolder->toPostDataKeepEmptyTelephone();
        $cfPassHolder->toPostDataKeepEmptyGSM();
        $cfPassHolder->toPostDataKeepEmptySchoolConsumerKey();

        return $cfPassHolder;
    }

    /**
     * @param OptInPreferences $optInPreferences
     *
     * @return \CultureFeed_Uitpas_Passholder_OptInPreferences
     */
    private function createCultureFeedOptInPreferences(OptInPreferences $optInPreferences) {
        $cfOptInPreferences = new \CultureFeed_Uitpas_Passholder_OptInPreferences();

        $cfOptInPreferences->optInServiceMails = $optInPreferences->hasOptInServiceMails();
        $cfOptInPreferences->optInMilestoneMails = $optInPreferences->hasOptInMilestoneMails();
        $cfOptInPreferences->optInInfoMails = $optInPreferences->hasOptInInfoMails();
        $cfOptInPreferences->optInSms = $optInPreferences->hasOptInSms();
        $cfOptInPreferences->optInPost = $optInPreferences->hasOptInPost();

        return $cfOptInPreferences;
    }
}
