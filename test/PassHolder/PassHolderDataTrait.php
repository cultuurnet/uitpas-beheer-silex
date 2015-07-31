<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\INSZNumber;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceEmail;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferences;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceSMS;
use ValueObjects\DateTime\Date;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

trait PassHolderDataTrait
{
    /**
     * @return PassHolder
     */
    public function getCompletePassHolderUpdate()
    {
        return (new PassHolder(
            (new Name(
                new StringLiteral('Layla'),
                new StringLiteral('Zyrani')
            ))->withMiddleName(
                new StringLiteral('Zoni')
            ),
            (new Address(
                new StringLiteral('1090'),
                new StringLiteral('Jette (Brussel)')
            ))->withStreet(
                new StringLiteral('Rue Perdue 101 /0003')
            ),
            (new BirthInformation(
                Date::fromNativeDateTime(new \DateTime('1976-09-13'))
            ))->withPlace(
                new StringLiteral('Casablanca')
            )
        ))->withINSZNumber(
            new INSZNumber('93051822361')
        )->withGender(
            Gender::FEMALE()
        )->withNationality(
            new StringLiteral('Maroc')
        )->withContactInformation(
            (new ContactInformation())
                ->withEmail(
                    new EmailAddress('zyrani_.hotmail.com@mailinator.com')
                )->withTelephoneNumber(
                    new StringLiteral('0488694231')
                )->withMobileNumber(
                    new StringLiteral('0499748596')
                )
        )->withPrivacyPreferences(
            new PrivacyPreferences(
                PrivacyPreferenceEmail::ALL(),
                PrivacyPreferenceSMS::NOTIFICATION()
            )
        );
    }

    /**
     * @return PassHolder
     */
    public function getCompletePassHolder()
    {
        return (new PassHolder(
            (new Name(
                new StringLiteral('Layla'),
                new StringLiteral('Zyrani')
            ))->withMiddleName(
                new StringLiteral('Zoni')
            ),
            (new Address(
                new StringLiteral('1090'),
                new StringLiteral('Jette (Brussel)')
            ))->withStreet(
                new StringLiteral('Rue Perdue 101 /0003')
            ),
            (new BirthInformation(
                Date::fromNativeDateTime(new \DateTime('1976-09-13'))
            ))->withPlace(
                new StringLiteral('Casablanca')
            )
        ))->withINSZNumber(
            new INSZNumber('93051822361')
        )->withGender(
            Gender::FEMALE()
        )->withNationality(
            new StringLiteral('Maroc')
        )->withPicture(
            new StringLiteral('R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=')
        )->withContactInformation(
            (new ContactInformation())
                ->withEmail(
                    new EmailAddress('zyrani_.hotmail.com@mailinator.com')
                )->withTelephoneNumber(
                    new StringLiteral('0488694231')
                )->withMobileNumber(
                    new StringLiteral('0499748596')
                )
        )->withPrivacyPreferences(
            new PrivacyPreferences(
                PrivacyPreferenceEmail::ALL(),
                PrivacyPreferenceSMS::NOTIFICATION()
            )
        )->withPoints(
            new Integer(20)
        );
    }
}
