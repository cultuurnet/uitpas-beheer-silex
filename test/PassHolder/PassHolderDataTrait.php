<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\CardSystemCollection;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutCollection;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuutStatus;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Address;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformation;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Gender;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\INSZNumber;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\OptInPreferences;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceEmail;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferences;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\PrivacyPreferenceSMS;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use CultuurNet\UiTPASBeheer\School\School;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPAS;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASCollection;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASStatus;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASType;
use CultuurNet\UiTPASBeheer\User\Properties\Uid;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
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
        )
        ->withRemarks(
            new Remarks(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed haec omittamus; Ecce aliud simile dissimile. Aliter homines, aliter philosophos loqui putas oportere? Cum ageremus, inquit, vitae beatum et eundem supremum diem, scribebamus haec. Propter nos enim illam, non propter eam nosmet ipsos diligimus.'
            )
        )
        ->withPicture(
            new StringLiteral('R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=')
        )
        ->withSchool(
            new School(
                new StringLiteral('920f8d53-abd0-40f1-a151-960098197785'),
                new StringLiteral('University of Life')
            )
        );
    }

    /**
     * @param Gender|null $gender
     *
     * @return PassHolder
     */
    public function getCompletePassHolder(Gender $gender = null)
    {
        if (!$gender) {
            $gender = Gender::FEMALE();
        }

        $cardSystem10 = new CardSystem(
            new CardSystemId('10'),
            new StringLiteral('UiTPAS Regio Aalst')
        );

        $cardSystem20 = new CardSystem(
            new CardSystemId('20'),
            new StringLiteral('UiTPAS Regio Kortrijk')
        );

        $cardSystem30 = new CardSystem(
            new CardSystemId('30'),
            new StringLiteral('UiTPAS Regio Brussel')
        );

        $cardSystem40 = new CardSystem(
            new CardSystemId('40'),
            new StringLiteral('UiTPAS Regio Leuven')
        );

        $kansenStatuten = (new KansenStatuutCollection())
            ->withKey(
                10,
                (new KansenStatuut(
                    new Date(
                        new Year('2015'),
                        Month::getByName('SEPTEMBER'),
                        new MonthDay(15)
                    )
                ))->withStatus(
                    KansenStatuutStatus::IN_GRACE_PERIOD()
                )->withCardSystem(
                    $cardSystem10
                )
            )
            ->withKey(
                30,
                (new KansenStatuut(
                    new Date(
                        new Year('2016'),
                        Month::getByName('SEPTEMBER'),
                        new MonthDay(15)
                    )
                ))->withStatus(
                    KansenStatuutStatus::EXPIRED()
                )->withCardSystem(
                    $cardSystem30
                )
            );

        $uitpasCollection = (new UiTPASCollection())
            ->with(
                new UiTPAS(
                    new UiTPASNumber('4567345678910'),
                    UiTPASStatus::ACTIVE(),
                    UiTPASType::CARD(),
                    $cardSystem10
                )
            )
            ->with(
                new UiTPAS(
                    new UiTPASNumber('4567345678902'),
                    UiTPASStatus::ACTIVE(),
                    UiTPASType::KEY(),
                    $cardSystem20
                )
            )
            ->with(
                new UiTPAS(
                    new UiTPASNumber('1256789944516'),
                    UiTPASStatus::BLOCKED(),
                    UiTPASType::STICKER(),
                    $cardSystem30
                )
            );

        $cardSystemCollection = (new CardSystemCollection())
            ->with($cardSystem10)
            ->with($cardSystem20)
            ->with($cardSystem30)
            ->with($cardSystem40);

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
        ))->withUid(
            new Uid('5')
        )->withINSZNumber(
            new INSZNumber('93051822361')
        )->withGender(
            $gender
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
        )->withKansenStatuten(
            $kansenStatuten
        )->withPrivacyPreferences(
            new PrivacyPreferences(
                PrivacyPreferenceEmail::ALL(),
                PrivacyPreferenceSMS::NOTIFICATION()
            )
        )->withPoints(
            new Integer(20)
        )->withUiTPASCollection(
            $uitpasCollection
        )->withCardSystems(
            $cardSystemCollection
        )->withRemarks(
            new Remarks(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed haec omittamus; Ecce aliud simile dissimile. Aliter homines, aliter philosophos loqui putas oportere? Cum ageremus, inquit, vitae beatum et eundem supremum diem, scribebamus haec. Propter nos enim illam, non propter eam nosmet ipsos diligimus.'
            )
        )
        ->withSchool(
            new School(
                new StringLiteral('920f8d53-abd0-40f1-a151-960098197785')
            )
        );
    }
}
