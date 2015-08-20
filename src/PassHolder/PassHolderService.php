<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberInvalidException;
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
        Passholder $passholder,
        VoucherNumber $voucherNumber = null
    ) {
        $existingPassholder = $this->getByUitpasNumber($uitpasNumber);

        if ($existingPassholder) {
            throw new UiTPASNumberAlreadyUsedException();
        };

        $cfPassHolder = $this->createCultureFeedPassholder($passholder);
        $cfPassHolder->uitpasNumber = $uitpasNumber->toNative();

        if ($voucherNumber) {
            // not sure how to pass a voucher along so I'm giving this a try
            $cfPassHolder->voucherNumber = $voucherNumber->toNative();
        }

        $UUIDString = $this->getUitpasService()->createPassholder(
            $cfPassHolder,
            $this->getCounterConsumerKey()
        );

        $UUID = UUID::fromNative($UUIDString);

        return $UUID;
    }

    /**
     * @param Passholder $passholder
     *
     * @return \CultureFeed_Uitpas_Passholder
     */
    private function createCultureFeedPassholder(Passholder $passholder)
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();

        $cfPassHolder->firstName =$passholder->getName()->getFirstName()->toNative();
        $cfPassHolder->name = $passholder->getName()->getLastName()->toNative();
        if ($passholder->getName()->getMiddleName()) {
            $cfPassHolder->secondName = $passholder
                ->getName()
                ->getMiddleName()
                ->toNative();
        }

        if ($passholder->getNationality()) {
            $cfPassHolder->nationality = $passholder
                ->getNationality()
                ->toNative();
        }

        $birthInformation = $passholder->getBirthInformation();

        if ($birthInformation->getPlace()) {
            $cfPassHolder->placeOfBirth = $birthInformation
                ->getPlace()
                ->toNative();
        }

        $cfPassHolder->dateOfBirth = $birthInformation
            ->getDate()
            ->toNativeDateTime()
            ->getTimestamp();

        if ($passholder->getGender()) {
            $cfPassHolder->gender = $this->getCfPassholderGenderForUpdate(
                $passholder->getGender()
            );
        }

        $address = $passholder->getAddress();

        if ($address->getStreet()) {
            $cfPassHolder->street = $address->getStreet()->toNative();
        }

        $cfPassHolder->city = $address->getCity()->toNative();
        $cfPassHolder->postalCode = $address->getPostalCode()->toNative();


        $contactInformation = $passholder->getContactInformation();
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

        $privacyPreferences = $passholder->getPrivacyPreferences();

        if ($privacyPreferences) {
            $cfPassHolder->emailPreference = $privacyPreferences
                ->getEmailPreference()
                ->toNative();
            $cfPassHolder->smsPreference = $privacyPreferences
                ->getSMSPreference()
                ->toNative();
        }

        if ($passholder->getINSZNumber()) {
            $cfPassHolder->inszNumber = $passholder
                ->getINSZNumber()
                ->toNative();
        }

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
