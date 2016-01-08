<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

class HumanReadableGender
{
    /**
     * @var Gender
     */
    private $gender;

    /**
     * @var Language
     */
    private $language;

    public function __construct(Gender $gender, Language $language)
    {
        $this->gender = $gender;
        $this->language = $language;
    }

    public function toNative()
    {
        $genderCode = $this->gender->toNative();
        $languageCode = $this->language->toNative();
        $readableGender = '';

        if ($languageCode == 'EN') {
            switch ($genderCode) {
                case 'MALE':
                    $readableGender = 'Man';
                    break;

                case 'FEMALE':
                    $readableGender = 'Woman';
                    break;
            }

        } elseif ($languageCode = 'NL') {
            switch ($genderCode) {
                case 'MALE':
                    $readableGender = 'Man';
                    break;

                case 'FEMALE':
                    $readableGender = 'Vrouw';
                    break;
            }
        }

        return $readableGender;
    }
}
