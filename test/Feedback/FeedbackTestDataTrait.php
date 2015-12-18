<?php

namespace CultuurNet\UiTPASBeheer\Feedback;

use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Web\EmailAddress;

trait FeedbackTestDataTrait
{
    /**
     * @return StringLiteral
     */
    public function getFromName()
    {
        return new StringLiteral('Alain');
    }

    /**
     * @return EmailAddress
     */
    public function getFromEmail()
    {
        return new EmailAddress('protput@heteiland.be');
    }

    /**
     * @return StringLiteral
     */
    public function getFromCounter()
    {
        return new StringLiteral('Het Eiland');
    }

    /**
     * @return StringLiteral
     */
    public function getMessage()
    {
        return new StringLiteral(
            'Het enige wat ik wil horen Guido, zijn vragen, antwoorden en punten. ' .
            'Zo is het beloofd aan Michel en zo zal het ook gebeuren.' . PHP_EOL . PHP_EOL .
            'Gewoon efkes communiceren. Oeh, gelijke stand of wat? ' .
            'Ja maar Frankie we hebbe elkaar allemaal op een andere manier ' .
            'leren kennen dus iedereen heeft een beetje gewonnen.' . PHP_EOL . PHP_EOL .
            'De kathedraal is wat bouwvallig maar euhm goei fundamenten.' . PHP_EOL . PHP_EOL .
            'Ho Alain voelde gij ook de lente in uwe buik? ' .
            'Nee, allee ja lentesla da zou kunne, van vanmorgen he tussen mijne boterham.' . PHP_EOL . PHP_EOL .
            'Ik vind het een fout signaal aan u mensen da ge nen drink geeft omdat ge gepromoveerd wordt. ' .
            'Ja. Maar dat is voor mijn verjaardag. Heb ik u nu juist betrapt op een leugen Michel.'
        );
    }

    /**
     * @return Feedback
     */
    public function getFeedback()
    {
        return new Feedback(
            $this->getFromName(),
            $this->getFromEmail(),
            $this->getFromCounter(),
            $this->getMessage()
        );
    }
}
