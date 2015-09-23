<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\Identity\UUID;

class PassHolderService extends CounterAwareUitpasService implements PassHolderServiceInterface
{
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
        $cfPassHolder = $this->createCultureFeedPassholder($passHolder);
        $cfPassHolder->uitpasNumber = $uitpasNumber->toNative();

        $this
            ->getUitpasService()
            ->updatePassholder(
                $cfPassHolder,
                $this->getCounterConsumerKey()
            );
    }

    /**
     * {@inheritdoc}
     **/
    public function register(
        UiTPASNumber $uitpasNumber,
        Passholder $passHolder,
        VoucherNumber $voucherNumber = null,
        KansenStatuut $kansenStatuut = null
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

        $UUIDString = $this->getUitpasService()->createPassholder(
            $cfPassHolder,
            $this->getCounterConsumerKey()
        );

        $UUID = UUID::fromNative($UUIDString);

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
            $cfPassHolder->gender = $this->getCfPassholderGenderForUpdate(
                $passHolder->getGender()
            );
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

        $cfPassHolder->toPostDataKeepEmptySecondName();
        $cfPassHolder->toPostDataKeepEmptyEmail();
        $cfPassHolder->toPostDataKeepEmptyMoreInfo();

        return $cfPassHolder;
    }

    /**
     * Get the right gender string value for updating a pass holder.
     *
     * Normally the gender is indicated by 'FEMALE' and 'MALE', when updating the
     * passholder though the values 'F' and 'M' need to be used.
     *
     * @param Gender $gender
     *
     * @return string
     */
    private function getCfPassholderGenderForUpdate(Gender $gender)
    {
        if ($gender->is(Gender::FEMALE())) {
            return 'F';
        }

        return 'M';
    }
}
