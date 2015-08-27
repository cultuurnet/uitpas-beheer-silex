<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
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
        VoucherNumber $voucherNumber = null,
        KansenStatuut $kansenstatuut = null
    ) {
        $existingPassholder = $this->getByUitpasNumber($uitpasNumber);

        if ($existingPassholder) {
            throw new UiTPASNumberAlreadyUsedException();
        };

        $cfPassHolder = $this->createCultureFeedPassholder($passholder);
        $cfPassHolder->uitpasNumber = $uitpasNumber->toNative();

        if ($voucherNumber) {
            $cfPassHolder->voucherNumber = $voucherNumber->toNative();
        }

        if ($uitpasNumber->hasKansenStatuut()) {
            if (is_null($kansenstatuut)) {
                throw new MissingPropertyException('kansenstatuut');
            } else {
                $cfPassHolder->kansenStatuut = true;
                $cfPassHolder->kansenStatuutEndDate = $kansenstatuut
                    ->getEndDate()
                    ->toNativeDateTime()
                    ->format('c');
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
     * @param Passholder $passholder
     *
     * @return \CultureFeed_Uitpas_Passholder
     */
    private function createCultureFeedPassholder(Passholder $passholder)
    {
        $cfPassholder = new \CultureFeed_Uitpas_Passholder();

        $cfPassholder->firstName =$passholder->getName()->getFirstName()->toNative();
        $cfPassholder->name = $passholder->getName()->getLastName()->toNative();
        if ($passholder->getName()->getMiddleName()) {
            $cfPassholder->secondName = $passholder
                ->getName()
                ->getMiddleName()
                ->toNative();
        }

        if ($passholder->getNationality()) {
            $cfPassholder->nationality = $passholder
                ->getNationality()
                ->toNative();
        }

        $birthInformation = $passholder->getBirthInformation();

        if ($birthInformation->getPlace()) {
            $cfPassholder->placeOfBirth = $birthInformation
                ->getPlace()
                ->toNative();
        }

        $cfPassholder->dateOfBirth = $birthInformation
            ->getDate()
            ->toNativeDateTime()
            ->getTimestamp();

        if ($passholder->getGender()) {
            $cfPassholder->gender = $this->getCfPassholderGenderForUpdate(
                $passholder->getGender()
            );
        }

        $address = $passholder->getAddress();

        if ($address->getStreet()) {
            $cfPassholder->street = $address->getStreet()->toNative();
        }

        $cfPassholder->city = $address->getCity()->toNative();
        $cfPassholder->postalCode = $address->getPostalCode()->toNative();


        $contactInformation = $passholder->getContactInformation();
        if ($contactInformation) {
            if ($contactInformation->getMobileNumber()) {
                $cfPassholder->gsm = $contactInformation
                    ->getMobileNumber()
                    ->toNative();
            }

            if ($contactInformation->getTelephoneNumber()) {
                $cfPassholder->telephone = $contactInformation
                    ->getTelephoneNumber()
                    ->toNative();
            }

            if ($contactInformation->getEmail()) {
                $cfPassholder->email = $contactInformation
                    ->getEmail()
                    ->toNative();
            }
        }

        $privacyPreferences = $passholder->getPrivacyPreferences();

        if ($privacyPreferences) {
            $cfPassholder->emailPreference = $privacyPreferences
                ->getEmailPreference()
                ->toNative();
            $cfPassholder->smsPreference = $privacyPreferences
                ->getSMSPreference()
                ->toNative();
        }

        if ($passholder->getINSZNumber()) {
            $cfPassholder->inszNumber = $passholder
                ->getINSZNumber()
                ->toNative();
        }

        $cfPassholder->toPostDataKeepEmptySecondName();

        return $cfPassholder;
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
