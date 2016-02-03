<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\Properties\Language;
use ValueObjects\StringLiteral\StringLiteral;

class HumanReadableGender extends StringLiteral
{
    /**
     * @var Gender
     */
    private $gender;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var array
     */
    private $readableGenders;

    public function __construct(Gender $gender, Language $language)
    {
        $this->gender = $gender;
        $this->language = $language;
        $this->readableGenders = [
            'EN' => [
                'MALE' => 'Man',
                'FEMALE' => 'Woman',
            ],
            'NL' => [
                'MALE' => 'Man',
                'FEMALE' => 'Vrouw',
            ],
        ];

        $value = $this->readableGenders[$this->language->toNative()][$this->gender->toNative()];

        parent::__construct($value);
    }
}
